<?php

declare(strict_types=1);

namespace App\Services;

use Spatie\SimpleExcel\SimpleExcelReader;

class PaymentSlipFileReader
{
    public function execute(string $filePath): \Generator
    {
        /**
         * Ready a big csv list by chunks to avoid memory exceptions using Generator
         */
        $iterator = SimpleExcelReader::create($filePath)
            ->useDelimiter(',')
            ->useHeaders(['name', 'governmentId', 'email', 'debtAmount', 'debtDueDate', 'debtId'])
            ->getRows()
            ->chunk(1) // itera a cada 100 registros ate o fim do csv
            ->getIterator();

        foreach ($iterator as $chunks) {
            // Disponibiliza esse chunk para processamento antes de carregar o proximo em memoria
            yield from $chunks;
        }
    }
}
