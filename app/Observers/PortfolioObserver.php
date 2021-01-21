<?php

namespace App\Observers;

use App\Models\Portfolio;
use App\Models\PortfolioSummary;

class PortfolioObserver
{

    public function creating(Portfolio $portfolio)
    {
        $portfolio->tenant_id = session('tenant_id');
    }

    /**
     * Handle the Portfolio "created" event.
     *
     * @param  \App\Models\Portfolio  $portfolio
     * @return void
     */
    public function created(Portfolio $portfolio)
    {
        //
    }

    /**
     * Handle the Portfolio "updated" event.
     *
     * @param  \App\Models\Portfolio  $portfolio
     * @return void
     */
    public function updated(Portfolio $portfolio)
    {
        
    }

    /**
     * Handle the Portfolio "deleted" event.
     *
     * @param  \App\Models\Portfolio  $portfolio
     * @return void
     */
    public function deleting(Portfolio $portfolio)
    {

    }

    /**
     * Handle the Portfolio "deleted" event.
     *
     * @param  \App\Models\Portfolio  $portfolio
     * @return void
     */
    public function deleted(Portfolio $portfolio)
    {
        //check if its the last record in the portfolio table, 
        //if yes, delete the stock from portfolio summary table
        
        $count = Portfolio::where('shareholder_id', $portfolio->shareholder_id)
                    ->where('stock_id', $portfolio->stock_id)->sum('quantity');        //returns 0 if not found
        
        if($count == 0){
            PortfolioSummary::where('shareholder_id', $portfolio->shareholder_id)
            ->where('stock_id', $portfolio->stock_id)->delete();
        }
        // info('observer deleted');

    }

    /**
     * Handle the Portfolio "restored" event.
     *
     * @param  \App\Models\Portfolio  $portfolio
     * @return void
     */
    public function restored(Portfolio $portfolio)
    {
        //
    }

    /**
     * Handle the Portfolio "force deleted" event.
     *
     * @param  \App\Models\Portfolio  $portfolio
     * @return void
     */
    public function forceDeleted(Portfolio $portfolio)
    {
        //
    }
}
