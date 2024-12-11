<?php

use Illuminate\Support\Facades\Route;

Route::post('/payment-slip/process-file', [\App\Http\Controllers\PaymentSlipFileProcessorController::class, 'index']);
