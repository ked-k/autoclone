<?php

namespace App\Providers;

use App\Models\FacilityInformation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
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
        Blade::directive('formatDate', function ($expression) {
            return "<?php echo date('d-M-Y', strtotime($expression)); ?>";
        });

        Blade::directive('formatDateTime', function ($date) {
            return "<?php echo date('d-M-Y H:i', strtotime($date)); ?>";
        });
        
        if ((new \App\Models\FacilityInformation)->getTable()) {
            View::share('facilityInfo', FacilityInformation::first());
        }
        Paginator::useBootstrapFive();
    }
}
