<?php

namespace App\Providers;

use App\Services\StockAlertService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        require_once app_path('Support/report_format.php');

        View::composer(['layouts.backendapp', 'dashboard'], function ($view) {
            $view->with(app(StockAlertService::class)->getAlerts());
        });
    }
}
