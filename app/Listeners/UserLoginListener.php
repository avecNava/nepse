<?php

namespace App\Listeners;

use App\Models\Shareholder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserLoginListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        Log::info('Login ', [$event->user->name]);

        //create session record for the tenant_id        
        session()->put('tenant_id', $event->user->id);

        //find shareholder_id and create a session record
        $shareholder = Shareholder::where('parent_id', Auth::id())->pluck('id')->first();
        
        if($shareholder){
            session()->put('shareholder_id', $shareholder);            
        }
        
    }
}
