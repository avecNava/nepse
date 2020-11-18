<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        //https://laravel.com/docs/8.x/database#listening-for-query-events
        // receive each SQL query executed by the  application,
        DB::listen(function ($query) {            
            Log::debug("SQL : " . $query->sql);
            // $query->bindings
            // $query->time
        });
    }
}
