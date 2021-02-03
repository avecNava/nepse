<?php

namespace App\Observers;

use App\Models\Portfolio;
use App\Models\PortfolioSummary;
use Illuminate\Support\Facades\Auth;

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
        
        $shareholder_id = $portfolio->shareholder_id;
        $stock_id = $portfolio->stock_id;

        $quantity =  Portfolio::where('shareholder_id', $shareholder_id)
                    ->whereNotNull('wacc_updated_at')
                    ->where('stock_id', $stock_id)
                    ->sum('quantity');                      //returns 0 if not found

        $effective_rate = Portfolio::calculateWACC($shareholder_id, $stock_id);
        
        $investment = Portfolio::where('shareholder_id', $shareholder_id)
                        ->where('stock_id', $stock_id)
                        ->whereNotNull('wacc_updated_at')
                        ->sum('total_amount');
        
            
        PortfolioSummary::updateOrCreate(
        [
            'shareholder_id' => $shareholder_id,
            'stock_id' => $stock_id,
        ],
        [
            'quantity' => $quantity,
            'investment' => $investment,
            'wacc' => $effective_rate,
            'last_modified_by' => Auth::id(),
        ]);
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
