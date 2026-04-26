<?php

namespace App\Providers;

use App\Services\AuthServices;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind AuthService as a singleton
        // (one instance shared for the whole request lifecycle)

        $this->app->singleton(AuthServices::class, function ($app) {
            return new AuthServices();
        });

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
