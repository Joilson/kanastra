<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Jobs\ProcessPaymentSlipFile;
use App\Services\Builder\PaymentSlipBuilderFactory;
use App\Services\Communication\Dispatcher\EmailDispatcher;
use App\Services\Communication\Providers\Customers\CustomerYourListAlreadyProcessed;
use App\Services\Communication\Providers\EndUsers\EndUserNewPaymentSlip;
use App\Services\PaymentSlipFileReader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;

class ProcessPaymentSlipFileTest extends TestCase
{
    # Nao deve persistir durante os testes
    use RefreshDatabase;

    public function testShouldProcessPaymentSlipFile(): void
    {

        $filePath = __DIR__ . "/../Fixtures/input.csv";
        $listener = new ProcessPaymentSlipFile($filePath);

        $paymentSlipFileReaderMock = $this->createMock(PaymentSlipFileReader::class);
        $paymentSlipFileReaderMock->expects($this->once())
            ->method('execute')
            ->willReturnCallback(function () {
                yield [
                    'name' => 'name',
                    'governmentId' => 'governmentId',
                    'email' => 'email',
                    'debtAmount' => 2,
                    'debtDueDate' => '2024-12-25',
                    'debtId' => 'debtId'
                ];
            });

        $factory = new PaymentSlipBuilderFactory();

        $dispatcher = $this->createMock(EmailDispatcher::class);
        $dispatcher->expects($this->exactly(2))->method('dispatch')->with(
            $this->logicalOr(
                $this->isInstanceOf(EndUserNewPaymentSlip::class),
                $this->isInstanceOf(CustomerYourListAlreadyProcessed::class)
            )
        );

        $listener->handle($paymentSlipFileReaderMock, $factory, $dispatcher);
    }
}
