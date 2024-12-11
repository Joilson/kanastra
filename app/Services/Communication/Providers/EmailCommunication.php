<?php

declare(strict_types=1);

namespace App\Services\Communication\Providers;

interface EmailCommunication extends Communication, \JsonSerializable
{
    public function sender(): string;

    public function receiver(): string;

    public function subject(): string;

    public function attachments(): array;
}
