<?php

namespace App\Providers;

use App\Jobs\ProcessPaymentSlipFile;
use App\Services\Builder\PaymentSlipBuilderFactory;
use App\Services\Communication\Dispatcher\EmailDispatcher;
use App\Services\PaymentSlipFileReader;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->bindMethod(
            [ProcessPaymentSlipFile::class, 'handle'], function (ProcessPaymentSlipFile $job, Application $app) {
                $job->handle(
                    $app->make(PaymentSlipFileReader::class),
                    $app->make(PaymentSlipBuilderFactory::class),
                    $app->make(EmailDispatcher::class)
                );
            }
        );
    }
}
