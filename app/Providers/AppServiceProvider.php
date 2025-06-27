<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // Importe a Facade View
use App\Models\Periodo; // Importe o Model Periodo

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
        // Compartilha os dados dos períodos com a view 'site.layout'
        View::composer('site.layout', function ($view) {
            $view->with('periodos', Periodo::orderBy('dataIni', 'desc')->get());
        });
    }

}
