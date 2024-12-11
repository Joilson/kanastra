<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\PaymentSlipFileReader;
use Illuminate\Foundation\Testing\TestCase;

class PaymentSlipFileReaderTest extends TestCase
{
    public function testShouldReadAndAssertCsvData(): void
    {
        $reader = new PaymentSlipFileReader();
        $rows = iterator_to_array($reader->execute(__DIR__ . "/../Fixtures/input.csv"));

        $this->assertCount(2, $rows);

        $this->assertEquals('John Doe', $rows[0]['name']);
        $this->assertEquals('John Doe2', $rows[1]['name']);

        // @todo add all columns asser (for first line)
    }
}
