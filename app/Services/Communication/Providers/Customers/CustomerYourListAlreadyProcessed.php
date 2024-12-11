<?php

declare(strict_types=1);

namespace App\Services\Communication\Providers\Customers;

use App\Services\Communication\Providers\EmailCommunication;

class CustomerYourListAlreadyProcessed implements EmailCommunication
{
    private string $body;

    public function __construct(private readonly array $info)
    {
        $this->body = "Hy your list is already processed with {$info['count']} success items";
    }

    public function body(): string
    {
        return $this->body;
    }

    public function sender(): string
    {
        return "larissa@kanastra.com";
    }

    public function receiver(): string
    {
        return $this->info['receiver'];
    }

    public function subject(): string
    {
        return "getting rich";
    }

    public function attachments(): array
    {
        return [];
    }

    public function jsonSerialize(): mixed
    {
        return [
            'body' => $this->body(),
            'sender' => $this->sender(),
            'receiver' => $this->receiver(),
            'subject' => $this->subject(),
            'attachments' => $this->attachments(),
            'customerEmail' => $this->info['receiver'],
        ];
    }
}
