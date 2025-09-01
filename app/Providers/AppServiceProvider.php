<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Providers\RouteServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class); // ✅ Register RouteServiceProvider
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
