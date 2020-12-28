<?php

namespace App\Providers;

use App\Events\UserRegisteredEvent;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Events\CurrentDeviceLogout;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Notifications\UserVerifyNotification;
use App\Listeners\UserLoginListener;
use App\Listeners\UserLogoutListener;
use App\Listeners\SendWelcomeMailListener;
use App\Listeners\RecordLoginDetailsListener;
use App\Listeners\SendVerificationMailListener;
use App\Events\CreateSampleRecordsEvent;
use App\Listeners\CreateSampleRecordsListener;
use Illuminate\Http\Request;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // when registered send verification link
        Registered::class => [
            UserVerifyNotification::class,
        ],
        // when verified send welcome mail, also create sample records
        Verified::class => [
            SendWelcomeMailListener::class,
            CreateSampleRecordsListener::class,
        ],
        /*set session for the current tenant_id when logged in*/
        Login::class => [
            UserLoginListener::class,
            RecordLoginDetailsListener::class,
        ],
        /*set session for the current tenant_id when logged out*/
        Logout::class => [
            UserLogoutListener::class,
        ],
        /*create sample records*/
        // CreateSampleRecordsEvent::class => [
        //     CreateSampleRecordsListener::class,
        // ],
        /*test only*/
        // UserRegisteredEvent::class => [
        //     SendVerificationMailListener::class,
        // ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
