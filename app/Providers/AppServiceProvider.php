<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AuthService as ServicesAuthService;
use App\Services\Interfaces\AuthServiceInterface;
use App\Services\OrderService;
use App\Services\Interfaces\OrderServiceInterface;
use App\Services\ProductService;
use App\Services\Interfaces\ProductServiceInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProductServiceInterface::class, ProductService::class);
        $this->app->bind(OrderServiceInterface::class, OrderService::class);
        $this->app->bind(AuthServiceInterface::class, ServicesAuthService::class);


    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
