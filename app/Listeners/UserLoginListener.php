<?php

namespace App\Listeners;

use App\Models\Shareholder;
use App\Models\Portfolio;
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
        info('User log in, listener', [$event->user->name]);

        //create session record for the tenant_id        
        session()->put('tenant_id', $event->user->id);

        //find shareholder_id and create a session record
        $shareholder = Shareholder::where('parent_id', Auth::id())->select('id')->first();
        if($shareholder){
            session()->put('shareholder_id', $shareholder['id']);
        }else{
            Log::error('Could not create session shareholder_id. Shareholder not found', [$shareholder]);
        }

        //add a random stock if the user is new (if no records for the given tenant_id)
        $count = \App\Models\Portfolio::where('tenant_id', $event->user->id)->count();
        if($count < 1){
            Portfolio::createRandomRecord();
        }
    }
}
