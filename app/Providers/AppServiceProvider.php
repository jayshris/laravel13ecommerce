<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// 1. Import the Schema facade
use Illuminate\Support\Facades\Schema;

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
        // 2. Set the default maximum string length for indexes
        Schema::defaultStringLength(191);
    }
}
