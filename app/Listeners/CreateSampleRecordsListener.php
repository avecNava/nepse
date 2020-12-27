<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;
use App\Models\Shareholder;

class CreateSampleRecordsListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        
        if (!empty($event->shareholder) ){

            $shareholder = $event->shareholder;
            
            $portfolio = Shareholder::where('parent_id', Auth::id())->withCount('portfolio as total')->first();
            if($portfolio->total < 1){
                \App\Models\Portfolio::createRandomRecord($shareholder);
            }
            
        }
    }
}
