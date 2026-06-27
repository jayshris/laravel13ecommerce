<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// 1. Import the Schema facade
use Illuminate\Support\Facades\Schema;

use Illuminate\Foundation\AliasLoader;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('cart',function(){
            return new \App\Services\CartService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 2. Set the default maximum string length for indexes
        Schema::defaultStringLength(191);


        $loader = AliasLoader::getInstance();
        $loader->alias('Cart',\App\Facades\Cart::class);
    }
}
