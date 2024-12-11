<?php

declare(strict_types=1);

namespace App\Services\Builder;

class Output implements \JsonSerializable
{
    public function __construct(
        public readonly string $filePath,
        public readonly \DateTime $createdAt,
        public readonly string $uuid
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): mixed
    {
        return [
            'filePath' => $this->filePath,
            'createdAt' => $this->createdAt->format(\DateTime::ATOM),
            'uuid' => $this->uuid
        ];
    }
}
