<?php

declare(strict_types=1);

namespace App\Services\Communication\Dispatcher;

use App\Services\Communication\Providers\EmailCommunication;
use Illuminate\Support\Facades\Log;

class EmailDispatcher
{
    public function dispatch(EmailCommunication $data): void
    {
        // All essential data to an email message
        Log::info(
            "Email communication dispatched", [
            'data' => $data->jsonSerialize(),
            ]
        );
    }
}
