<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\AI\GroqRefundRequestAnalyzer;
use App\Contracts\RefundRequestAnalyzer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        require_once app_path('Helpers/helpers.php');
        $this->app->bind(
            RefundRequestAnalyzer::class,
            GroqRefundRequestAnalyzer::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
