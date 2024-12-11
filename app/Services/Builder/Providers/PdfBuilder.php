<?php

declare(strict_types=1);

namespace App\Services\Builder\Providers;

use App\Models\PaymentSlip;
use App\Services\Builder\Output;
use App\Services\Builder\Type;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PdfBuilder implements Builder
{
    public function build(PaymentSlip $entity): Output
    {
        Log::info("Payment Slip Builder is requested for new pdf file {$entity->debt_id}");

        $fileName = Str::uuid()->toString();
        $path = Storage::path("generated/$fileName.pdv");
        Storage::put($path, "");

        return new Output(
            "generated/$fileName.pdv",
            new \DateTime(),
            $entity->debt_id
        );
    }

    public function isForType(Type $type): bool
    {
        return $type === Type::PDF;
    }
}
