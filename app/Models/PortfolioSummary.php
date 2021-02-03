<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Traits\BelongsToTenant;
use App\Models\Portfolio;
use App\Services\UtilityService;
use App\Models\Shareholder;

class PortfolioSummary extends Model
{
    use HasFactory, BelongsToTenant;
    
    protected $guarded = [];

    public function shareholder()
    {
        return $this->belongsTo('App\Models\Shareholder','shareholder_id');
    }

    public function share()
    {
        return $this->belongsTo('App\Models\Stock','stock_id');
    }

    // public function stockPrice()
    // {
    //     return $this->belongsTo('App\Models\StockPrice','stock_id','stock_id');
    // }

    public function price()
    {
        $transaction_date = StockPrice::getLastDate();
        return $this->belongsTo('App\Models\StockPrice','stock_id','stock_id');
    }


    public function scopeLatestPrice($query)
    {
        return $query->where('latest','=',TRUE);
    }
    
    /**
     * update or create portfolio summary table data based on portfolio table
     * input: shareholder_id, stock_id
     */
    public static function updateCascadePortfoliSummaries(int $shareholder_id, int $stock_id)
    {
        
        //get aggregate purchases, aggregate sales and average rates. Calculate net quantity, Add to the summary table
        $quantity =  Portfolio::where('shareholder_id', $shareholder_id)
                    ->whereNotNull('wacc_updated_at')
                    ->where('stock_id', $stock_id)
                    ->sum('quantity');                      //returns 0 if not found

        $effective_rate = Portfolio::calculateWACC($shareholder_id, $stock_id);
        
        $investment = Portfolio::where('shareholder_id', $shareholder_id)
                        ->where('stock_id', $stock_id)
                        ->whereNotNull('wacc_updated_at')
                        ->sum('total_amount');
        
        try {
            
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
        
                    
        } catch (\Throwable $th) {
            UtilityService::createLog('updateCascadePortfoliSummaries', $th);
        }
        
    }

}
