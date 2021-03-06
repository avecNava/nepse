<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Traits\BelongsToTenant;
use App\Services\UtilityService;
use App\Models\PortfolioSummary;
use App\Models\Portfolio;
use App\Models\SalesBasket;
use App\Models\StockPrice;
use Illuminate\Support\Facades\DB;
// use App\Services\UtilityService;


class Portfolio extends Model
{
    use HasFactory, BelongsToTenant;

    protected $guarded = [];

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
            $portfolio->dp_amount = $request->dp_amount;
            $portfolio->total_amount = $request->total_amount;
            $portfolio->base_amount = $request->base_amount;
            $portfolio->receipt_number = $request->receipt_number;
            $portfolio->tags = $request->tags;
            $portfolio->broker_no = $request->broker;
            $portfolio->offer_id = $request->offer;
            $portfolio->purchase_date = $request->purchase_date;
            $portfolio->wacc_updated_at = Carbon::now();
            $portfolio->last_modified_by = Auth::id();
            $portfolio->save();         //returns true on success
            
        } catch (\Throwable $th) {
            
            UtilityService::createLog('createPortfolio', $th);

            return response()->json([
                'status' => 'error',
                'message' =>  'Create portfolio error!! ' . $th->getMessage(),
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
                //dd($request->broker_commission);
                $portfolio->quantity = $request->quantity;
                $portfolio->unit_cost = $request->unit_cost;
                $portfolio->effective_rate = $request->effective_rate;
                $portfolio->broker_commission = $request->broker_commission;
                $portfolio->sebon_commission = $request->sebon_commission;
                $portfolio->dp_amount = $request->dp_amount;
                $portfolio->total_amount = $request->total_amount;
                $portfolio->base_amount = $request->base_amount;
                $portfolio->tags = $request->tags;
                $portfolio->receipt_number = $request->receipt_number;
                $portfolio->broker_no = $request->broker;
                $portfolio->offer_id = $request->offer;
                $portfolio->wacc_updated_at = Carbon::now();
                $portfolio->purchase_date = $request->purchase_date ? $request->purchase_date : Carbon::now();
                $portfolio->last_modified_by = Auth::id();
                $portfolio->save();

            } catch (\Throwable $th) {

                UtilityService::createLog('updatePortfolio', $th);

                return response()->json([
                    'status' => 'error',
                    'message' =>  'Portfolio not updated. Error message: ' . $th->getMessage(),
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
            $offers1 =['IPO','RIGHTS','BONUS','OTHERS','OTHER'];

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
                'wacc_updated_at' => in_array($row['offer_code'], $offers1) ? Carbon::now() : null,
            ]);
        }
    }

    /**
     * create random record for the supplied shareholder
     */
    public static function createRandomRecord($shareholder)
    {

        //1. get random 3 stocks 
        info('1. Obtained random stocks');
        $record = DB::table('stock_prices')->where('latest',true)->select('stock_id','close_price')->inRandomOrder()->take(2)->get();
        $qty = collect([10, 20, 30, 40, 50, 100]);
        $quantity = $qty->random();
        
        //2. add to the portfolio table
        $record->each(function($item) use($quantity, $shareholder) {

            $portfolio = new Portfolio;
            $portfolio->shareholder_id = $shareholder;
            $portfolio->stock_id = $item->stock_id;
            $portfolio->quantity = $quantity;
            $portfolio->unit_cost = 100;
            $portfolio->effective_rate = 100;
            $portfolio->total_amount = $quantity*100;
            $portfolio->offer_id = 1;
            $portfolio->tags = 'sample';
            $portfolio->remarks = "sample record";
            $portfolio->purchase_date = Carbon::now();
            $portfolio->wacc_updated_at = Carbon::now();
            $portfolio->last_modified_by = Auth::id();
            $portfolio->save();
        
        });

        //3. update the portfolio_summary table
        $record->each(function($item) use($shareholder) {
            PortfolioSummary::updateCascadePortfoliSummaries(
                $shareholder,
                $item->stock_id
            );
        });

        Log::info('Created sample portfolio', [optional(Auth::user())->name]);
    }

    public static function calculateWACC(int $shareholder, int $stock)
    {
        //only get records for the given shareholder, given stock whose wacc has been updated
        $portfolios = Portfolio::where('shareholder_id', $shareholder)
                    ->where('stock_id', $stock)
                    ->whereNotNull('wacc_updated_at')
                    ->get();
        
        if(!empty($portfolios)){

            $investment = $portfolios->sum(function($item){
                return $item->quantity * $item->effective_rate;
            });

            $quantity = $portfolios->sum('quantity');

            if($quantity>0)
                return round($investment / $quantity, 2);
            else {
                return 0;
            }
        }
        
        return 0;
    }

    public static function salesAdjustment($portfolio_id)
    {
        $portfolio = DB::table('portfolios as p')
            ->join('sales_basket as b','b.portfolio_id','p.id')
            ->select('b.*', 'p.quantity as total')
            ->where('p.id', $portfolio_id)
            ->first();
        
        //if all quantity is sold, delete entry from Portfolio
        //else update portfolio with the difference
        if($portfolio->quantity == $portfolio->total){
            Portfolio::destroy($portfolio_id);
        }else{
            $record = Portfolio::find($portfolio_id);            
            if(!empty($record)){
                $record->quantity = $portfolio->total - $portfolio->quantity;
                $record->save();
                Log::info('Portfolio deducted', 
                    [   'Original'=>$portfolio->total, 
                        'New quantity'=>$record->quantity,
                        'User'=>Auth::id() . '-' . Auth::user()->name, 
                        'Portfolio id' => $portfolio_id,
                ]);
            }
        }
    }

}
