<?php

declare(strict_types=1);

namespace App\Services\Communication\Providers\EndUsers;

use App\Models\PaymentSlip;
use App\Services\Builder\Output;
use App\Services\Communication\Providers\EmailCommunication;

class EndUserNewPaymentSlip implements EmailCommunication
{
    private string $body;

    public function __construct(
        private readonly PaymentSlip $paymentSlip,
        private readonly Output $data
    ) {
        $this->body = "Hi {$this->paymentSlip->name} new payment was available for payment, please see attachments.";
    }

    public function body(): string
    {
        return $this->body;
    }

    public function sender(): string
    {
        return "payments@kanastra.com";
    }

    public function receiver(): string
    {
        return $this->paymentSlip->email;
    }

    public function subject(): string
    {
        return "Hy {$this->paymentSlip->name}, you will be poorer";
    }

    public function attachments(): array
    {
        return [$this->data->filePath];
    }

    public function jsonSerialize(): array
    {
        return [
            'body' => $this->body(),
            'sender' => $this->sender(),
            'receiver' => $this->receiver(),
            'subject' => $this->subject(),
            'attachments' => $this->attachments(),
            'paymentSlipDebtId' => $this->paymentSlip->debt_id,
        ];
    }
}
