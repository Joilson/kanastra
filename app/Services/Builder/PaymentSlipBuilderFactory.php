<?php

declare(strict_types=1);

namespace App\Services\Builder;

use App\Services\Builder\Providers\Builder;
use App\Services\Builder\Providers\PdfBuilder;
use http\Exception\RuntimeException;

class PaymentSlipBuilderFactory
{
    /**
     * @var array<Builder> 
     */
    private readonly array $builders;

    public function __construct()
    {
        // @todo utilizar algo nativo do Laravel para encontrar essas instancias por tag ou interface
        $this->builders = [
            PdfBuilder::class => new PdfBuilder(),
        ];
    }

    public function forType(Type $type): Builder
    {
        foreach ($this->builders as $builder) {
            if ($builder->isForType($type)) {
                return $builder;
            }
        }

        throw new RuntimeException("Builder for type {$type->value} not found");
    }
}
