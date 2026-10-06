<?php

namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <-- N'oubliez pas d'importer cette classe

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
        // Force le HTTPS lorsque l'application est en production sur Render
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
