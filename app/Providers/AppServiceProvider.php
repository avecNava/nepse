<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
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
            Log::info("SQL : " . $query->sql);
            // $query->bindings
            // $query->time
        });

        //https://stackoverflow.com/questions/35337007/how-to-notify-the-user-that-a-job-has-completed-in-laravel
        //how-to-notify-the-user-that-a-job-has-completed-in-laravel
        
        Queue::before(function (JobProcessing $event) {
            // $event->connectionName
            // $event->job
            // $event->job->payload()
        });

        Queue::after(function (JobProcessed $event) {
            info(
                'finished sending mail : ' , 
                [
                    $event->connectionName,
                    $event->job,
                    // $event->job->payload()
                ]
            );
        });
    }
}
