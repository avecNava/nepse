<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;
use App\Models\Portfolio;
use App\Models\PortfolioSummary;
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
        if (session()->has('shareholder_id')){
            
            $shareholder = session()->get('shareholder_id');
            
            //create a default shareholder (group)
            Shareholder::createSampleRecord($shareholder);

            //check if the user has any records in PortfolioSummary table
            $records = PortfolioSummary::where('shareholder_id', $shareholder)->count('id');
            if($records == 0){
                Portfolio::createRandomRecord($shareholder);
            }
            
        }
    }
}
