<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;
use App\Models\PortfolioSummary;

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
        if (session()->has('shareholder_id')){

            //check if the user has any records in PortfolioSummary table
            $shareholder = session()->get('shareholder_id');
            $records = PortfolioSummary::where('shareholder_id', $shareholder)->count('id');
            if($records == 0){
                \App\Models\Portfolio::createRandomRecord($shareholder);
            }
            
        }
    }
}
