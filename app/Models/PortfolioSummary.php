<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PortfolioSummary extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function shareholder()
    {
        return $this->belongsTo('App\Models\Shareholder','shareholder_id');
    }

    public function share()
    {
        return $this->belongsTo('App\Models\Stock','stock_id');
    }

    public function stockPrice()
    {
        return $this->belongsTo('App\Models\StockPrice','stock_id','stock_id');
    }

    public function price()
    {
        $transaction_date = StockPrice::getLastDate();
        return $this->belongsTo('App\Models\StockPrice','stock_id','stock_id')->where('transaction_date','=',$transaction_date);
    }
    
    /**
     * update or create portfolio summary table data based on portfolio table
     * input: shareholder_id, stock_id
     */
    public static function updateCascadePortfoliSummaries(int $shareholder_id, int $stock_id)
    {
        //get aggregate purchases, aggregate sales and average rates. Calculate net quantity, Add to the summary table
        
        $total_purchases = 
            Portfolio::where('shareholder_id', $shareholder_id)
            ->where('stock_id', $stock_id)
            ->sum('quantity');
        
        $total_sales = 
            Sales::where('shareholder_id', $shareholder_id)
            ->where('stock_id', $stock_id)
            ->sum('quantity');
        
        $total_quantity = $total_purchases - $total_sales;
        
        $wacc_rate = 
            Portfolio::where('shareholder_id', $shareholder_id)
            ->where('stock_id', $stock_id)
            ->avg('effective_rate');
        
        PortfolioSummary::updateOrCreate(
        [
            'shareholder_id' => $shareholder_id,
            'stock_id' => $stock_id,
        ],
        [
            'total_quantity' => $total_quantity,
            'wacc_rate' => $wacc_rate,
            'last_modified_by' => Auth::id(),
        ]);

    }
    
}
