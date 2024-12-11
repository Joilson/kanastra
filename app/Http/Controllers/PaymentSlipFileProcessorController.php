<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPaymentSlipFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @TODO Adicionar apidoc e authentication
 */
class PaymentSlipFileProcessorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate(
            [
                'file' => 'required|file|mimes:csv,txt',
            ]
        );

        try {
            //@todo Move to "AWS s3?" before deploy to production
            $fileName = Str::uuid()->toString();
            $persistedFilePath = $request->file('file')->storeAs('lists/', "{$fileName}.csv");


            ProcessPaymentSlipFile::dispatch(Storage::path($persistedFilePath));
        } catch (\Exception $exception) {
            Log::error(
                'Error during processing payment slip: ', [
                    'exception' => $exception,
                    'persistedFilePath' => $persistedFilePath ?? null
                ]
            );

            return response()->json(["fail" => "An error occurred while processing the payment slip."]);
        }

        Log::info(
            'A list of payment slips has been imported, waiting to process in the background', [
                'persistedFilePath' => $persistedFilePath
            ]
        );

        return response()->json(["message" => "Success, your file has been imported and is processing"]);
    }
}
