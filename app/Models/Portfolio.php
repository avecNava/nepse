<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Scopes\TenantScope;

class Portfolio extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected static function booted()
    {
        static::addGlobalScope(new TenantScope);
    }

    public function shareholder()
    {
        return $this->belongsTo('App\Models\Shareholder','shareholder_id');
    }

    public function share()
    {
        return $this->belongsTo(Stock::class,'stock_id');
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

    public function scopeNonZero($query)
    {
        return $query->where('quantity','>',0);
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
        if(empty($request)){
            
            return response()->json([
                'status' => 'error',
                'message' =>  'Empty request received',
            ]);
        }
        
        try {
            $portfolio = new Portfolio;
            $portfolio->shareholder_id = $request->shareholder;
            $portfolio->stock_id = $request->stock;
            $portfolio->quantity = $request->quantity;
            $portfolio->unit_cost = $request->unit_cost;
            $portfolio->effective_rate = $request->effective_rate;
            $portfolio->broker_commission = $request->broker_commission;
            $portfolio->sebon_commission = $request->sebon_commission;
            $portfolio->total_amount = $request->total_amount;
            $portfolio->receipt_number = $request->receipt_number;
            $portfolio->tags = $request->tags;
            $portfolio->broker_no = $request->broker;
            $portfolio->offer_id = $request->offer;
            $portfolio->purchase_date = $request->purchase_date;
            $portfolio->last_modified_by = Auth::id();
            $portfolio->save();         //returns true on success
            
        } catch (\Throwable $th) {
            
            $error = [
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ];
            Log::error('New Portfolio Error! ', $error);

            return response()->json([
                'status' => 'error',
                'message' =>  'New Portfolio Error!!' . $error['message'],
            ]);

        }

        return response()->json([
            'status' => 'success',
            'message' =>  'Portfolio created successfully',
        ]);
        
    }
    
    public static function updatePortfolio(Request $request)
    {
        if(!empty($request)){

            try {
                
                $portfolio = Portfolio::find($request->id);
                
                $portfolio->quantity = $request->quantity;
                $portfolio->unit_cost = $request->unit_cost;
                $portfolio->effective_rate = $request->effective_rate;
                $portfolio->broker_commission = $request->broker_commission;
                $portfolio->sebon_commission = $request->sebon_commission;
                $portfolio->total_amount = $request->total_amount;
                $portfolio->tags = $request->tags;
                $portfolio->receipt_number = $request->receipt_number;
                $portfolio->broker_no = $request->broker;
                $portfolio->offer_id = $request->offer;
                $portfolio->purchase_date = $request->purchase_date ? $request->purchase_date : Carbon::now();
                $portfolio->last_modified_by = Auth::id();
                $portfolio->save();

            } catch (\Throwable $th) {

                $error = [
                    'message' => $th->getMessage(),
                    'line' => $th->getLine(),
                    'file' => $th->getFile(),
                ];
                Log::error('Portfolio update mismatch in portfolio and portfolio_summary table', $error);

                return response()->json([
                    'status' => 'error',
                    'message' =>  'Portfolio not updated. Error message: ' . $error['message'],
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' =>  'Portfolio updated successfully',
            ]);
        }    
    }

    /**
     * update or create portfolios based on input object
     * used by MeroShare for mass importing shares from meroshare portfolio
     */
    public static function updateOrCreatePortfolios($portfolios)
    {
        
        foreach ($portfolios as $row) {
 
            //update record if the following five attributes are met,
            //else not create a new record with the following attributes

            //if IPO, unit cost and effective rate = 100, BONUS,it will be 0, total_amount is qty*effective_rate
            $offers =['IPO','RIGHTS'];

            Portfolio::updateOrCreate(
            [
                'stock_id' => $row['stock_id'], 
                'shareholder_id' => $row['shareholder_id'],
                'offer_id' => $row['offer_id'],
                'quantity' => $row['quantity'], 
                'purchase_date' => $row['transaction_date'],
            ],
            [
                'offer_id' => $row['offer_id'],
                'last_modified_by' => Auth::id(),
                'remarks' => $row['remarks'],
                'unit_cost' => in_array($row['offer_code'], $offers) ? 100 : 0,
                'effective_rate' => in_array($row['offer_code'], $offers) ? 100 : 0,
                'total_amount' => in_array($row['offer_code'], $offers) ? $row['quantity']*100 : 0,                
            ]);
        }
    }

    
}
