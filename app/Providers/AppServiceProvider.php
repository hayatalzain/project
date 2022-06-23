<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
       Schema::defaultStringLength(191);
     //  \Carbon\Carbon::setLocale('ar');

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
         $this->app->bind('path.public', function () {
            return dirname(base_path()). DIRECTORY_SEPARATOR;
        });
        $this->app->bind('path.asset', function () {
            return dirname(base_path()). DIRECTORY_SEPARATOR;
        });



    }
}
