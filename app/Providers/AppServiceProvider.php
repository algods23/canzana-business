<?php

namespace App\Providers;

use App\Support\Analytics;
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
        View::composer('components.sidebar', function ($view): void {
            $view->with('sidebarStats', Analytics::dashboardStats());
        });
    }
}
