<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL; // 1. Importa esta clase

use Illuminate\Support\ServiceProvider;

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
        \Illuminate\Pagination\Paginator::useBootstrapFive();
        // 2. Fuerza HTTPS si el entorno es producción
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
