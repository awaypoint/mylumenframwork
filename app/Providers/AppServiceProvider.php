<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
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

    public function boot()
    {
        date_default_timezone_set('PRC');

        if (env('PRINT_SQL', false)) {
            DB::listen(function ($query) {
                echo $query->sql, PHP_EOL;
                die;
                //echo $query->time,PHP_EOL;
            });
        }
    }
}
