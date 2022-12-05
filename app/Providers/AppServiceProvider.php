<?php

namespace App\Providers;

use App\Models\FacilityInformation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ((new \App\Models\FacilityInformation)->getTable()) {
            View::share('facilityInfo', FacilityInformation::first());
        }
        Paginator::useBootstrapFive();
    }
}
