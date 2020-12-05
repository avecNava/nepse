<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Portfolio extends Model
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

    public function lastPrice()
    {
        $transaction_date = StockPrice::getLastDate();
        return $this->belongsTo('App\Models\StockPrice','stock_id','stock_id')->where('transaction_date','=',$transaction_date);
    }   

    /**
     * get sector name via stocks table -> hasOneThrough
     * //https://laravel.com/docs/8.x/eloquent-relationships#has-one-through
     * 
     *  first argument : name of final model we wish to access (ie, Sectors)
     *  second argument : name of intermediate model
     *  third argument : name of foreign key on intermediate model
     *  fourth argument : name of foreign key on final model
     * 
     */
    public function sector()
    {
       return $this->hasOneThrough('App\Models\StockSector','App\Models\Stock','id','id');
    }   

    public static function createPortfolio(Request $request)
    {
        if(!empty($request)){

            $portfolio = new Portfolio;

            $portfolio->shareholder_id = $request->shareholder_id;
            $portfolio->stock_id = $request->shareholder_id;
            $portfolio->quantity = $request->quantity;
            $portfolio->unit_cost = $request->unit_cost;
            $portfolio->total_amount = $request->total_amount;
            $portfolio->effective_rate = $request->effective_rate;
            $portfolio->receipt_number = $request->receipt_number;
            $portfolio->broker_no = $request->broker_number;
            $portfolio->offer_id = $request->offer;
            $portfolio->last_modified_by = Auth::id();
            $portfolio->save();
        
        }
        
    }
    
    public static function updatePortfolio(Request $request)
    {
        if(!empty($request)){

            $portfolio = Portfolio::find($request->id);

            $portfolio->quantity = $request->quantity;
            $portfolio->unit_cost = $request->unit_cost;
            $portfolio->total_amount = $request->total_amount;
            $portfolio->effective_rate = $request->effective_rate;
            $portfolio->receipt_number = $request->receipt_number;
            $portfolio->broker_no = $request->broker_number;
            $portfolio->offer_id = $request->offer;
            $portfolio->last_modified_by = Auth::id();
            $portfolio->save();
        
        }
        
    }
    
}
