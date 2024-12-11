<?php

namespace App\Services\Builder\Providers;

use App\Models\PaymentSlip;
use App\Services\Builder\Output;
use App\Services\Builder\Type;

interface Builder
{
    public function build(PaymentSlip $entity): Output;

    public function isForType(Type $type): bool;
}
