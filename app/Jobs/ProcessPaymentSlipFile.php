<?php

namespace App\Jobs;

use App\Models\PaymentSlip;
use App\Services\Builder\PaymentSlipBuilderFactory;
use App\Services\Builder\Type;
use App\Services\Communication\Dispatcher\EmailDispatcher;
use App\Services\Communication\Providers\Customers\CustomerYourListAlreadyProcessed;
use App\Services\Communication\Providers\EndUsers\EndUserNewPaymentSlip;
use App\Services\PaymentSlipFileReader;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\DeadlockException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPaymentSlipFile implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @todo Mover essa fila para o um gerenciador mais esperto e monitoravel, como SQS, Rabbit, Kafka
     */
    public function __construct(private readonly string $filePath)
    {
    }

    public function handle(
        PaymentSlipFileReader $reader,
        PaymentSlipBuilderFactory $builderFactory,
        EmailDispatcher $dispatcher
    ): void
    {
        Log::info('Processing payment slip: ' . $this->filePath);

        $count = 0;
        foreach ($reader->execute($this->filePath) as $csvRow) {

            // @todo use repositories
            $existent = PaymentSlip::where('debt_id', '=', $csvRow['debtId'])->first();
            if ($existent) {
                Log::debug("Ignoring already existent payment slip: " . $csvRow['debtId']);
                continue;
            }

            $entity = PaymentSlip::fromCsv($csvRow);
            $entity->save();

            $out = $builderFactory->forType(Type::PDF)->build($entity);

            Log::info(
                'Payment slip file was created to send for customer', [
                    'generated' => $out->jsonSerialize()
                ]
            );

            /**
             * @todo criar uma fila que processa o envio das comunicaçoes
             *       aqui deve apenas dispara-las.
             */
            $dispatcher->dispatch(new EndUserNewPaymentSlip($entity, $out));
            $dispatcher->dispatch(new CustomerYourListAlreadyProcessed(['receiver' => 'john@itau.com', 'count' => $count]));
        }

        // removing msg from queue
        $this->delete();
    }

    public function failed(\Throwable $exception)
    {
        if ($exception instanceof DeadlockException) {
            // Se o problema for DB sobrecarregado, deve ser reprocessada daqui 10s
            $this->release(['delay' => 10]);
        }

        /**
         * @todo O processamento aqui e um ponto critico,
         *
         * se falhar, podemos perder um pagamento.
         * Entao: mandar uma msg para um canal do slack ou um email (com visibilidade rapida)
         */

        /**
         * @todo Mover para DLX para ser reprocessado futuramente
         */


        Log::error('ProcessPaymentSlipFile FAIL: ' . $exception->getMessage());
    }
}
