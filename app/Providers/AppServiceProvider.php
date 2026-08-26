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
        View::composer(['layouts.backendapp', 'dashboard'], function ($view) {
            $view->with(app(StockAlertService::class)->getAlerts());
        });
    }
}
