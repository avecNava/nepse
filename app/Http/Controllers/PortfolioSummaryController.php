<?php

namespace App\Http\Controllers;

use App\Models\MeroShare;
use App\Models\Shareholder;
use App\Models\PortfolioSummary;
use App\Models\StockPrice;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PortfolioSummaryController extends Controller
{
    
    public function __constructor()
    {
        
        // Auth::loginUsingId(1);        
        // Auth::loginUsingId(1, true);         // Login and "remember" the given user...
        $this->middleware('auth');
        
    }

    /**
     * display the portfolio summary
     * if shareholder_id is null, show portfolio for the parent 
     * otherwise show portfolio for the selected user
     */
    public function index($username, $member = null)
    {
        //if shareholder_id is null, get "ALL Portfolio" [current user and all shareholders under the current user]
        //else load the portfolio for the given shareholder_id
        $user_id = Auth::id();
        $shareholder_id = [$member];       //make the varible array
        if(empty($member)){
            $shareholder_id = Shareholder::where('parent_id', $user_id)->pluck('id')->all();        //return Array            
        }
        
        //lookup data
        $shareholders = Shareholder::where('parent_id', $user_id)->get()    ;       
        $transaction_date = StockPrice::getLastDate();
        // $portfolios = Portfolio::where('shareholder_id', $shareholder)
        //                 ->with(['shareholder','share','stockPrice'=>function($q) use($transaction_date) {
        //                     $q->where('transaction_date', '>=', $transaction_date);
        //                   }])->get();
        
        $portfolios = DB::table('portfolio_summaries')
            ->join('stocks', 'stocks.id', '=', 'portfolio_summaries.stock_id')
            ->join('shareholders', function($join) use($shareholder_id){
                $join->on('shareholders.id', '=', 'portfolio_summaries.shareholder_id')
                    ->whereIn('shareholders.id', $shareholder_id);
            })
            ->join('stock_prices', 'stock_prices.stock_id', '=', 'portfolio_summaries.stock_id')
            ->select('portfolio_summaries.*','stocks.*', 'shareholders.*','stock_prices.*')
            ->where('stock_prices.transaction_date','=', $transaction_date)            
            ->orderBy('stocks.symbol')
            ->get();

        return view("portfolio", 
            [
                'portfolios' => $portfolios,
                'shareholders' => $shareholders,
                'shareholder_id' => empty($member) ? 0 : $member,
                'transaction_date' => $transaction_date,
            ]
        );
        
    }
    
    
    /**
     * reads data from MeroShare table (meroshare_transactions) along with related data from Shares table
     * and forms array object
     * http://dev.nepse/meroshare/import-transaction
     * NOTE: this function is for testing purpose only and not used in PRODUCTION
     * The same logic is applied in storeToPortfolio() function and used in PRODUCTION
     */
    public function portfolio()
    {   
        $user_id = 2;
        $total_dr = 0;
        $total_cr = 0;
        $collection = collect([]);

        // $transactions = MeroShare::join('stocks', 'stocks.symbol', '=', 'meroshare_transactions.symbol')
        //     ->get(['meroshare_transactions.*','stocks.id']);

        //https://laravel.com/docs/8.x/eloquent-relationships#constraining-eager-loads
        // $transactions = MeroShare::with(['share' => function ($query) {
        //     $query->where('title', 'like', '%first%');
        // }])->get();
        
        $transactions = MeroShare::where('shareholder_id', $user_id)->with('share:id,symbol,security_name')->get();
      
        
        $temp = $transactions->groupBy('symbol');
        $temp->map(function($item) use($collection, $total_cr, $total_dr){
            
            foreach ($item as $value) {
                
                $total_cr += empty($value->credit_quantity) ? 0 : $value->credit_quantity;
                $total_dr += empty($value->debit_quantity) ? 0 : $value->debit_quantity;
                
                //combine data from main and related table and bind add to collection
                $portfolio = array(
                    'id' => $value->id,
                    'symbol' => $value->symbol,
                    'stock_id' => $value->id,
                    'quantity' => $total_cr - $total_dr,
                    'user_id' => $value->shareholder_id,
                    'shareholder_id' => $value->shareholder_id,
                    'security_name' => empty($value->share) ? null :  $value->share->security_name,
                    'stock_id' =>  empty($value->share) ? null : $value->share->id,
                );

                $collection->push( $portfolio );
                
            };
        });

        // $collection->dd();
        
        //add or update the cleaned data to the protfolio table based on stock_id and shareholder-id
        //one shareholder = one unique stock
        foreach ($collection as $row) {
            Portfolio::updateOrCreate(
                [
                    'stock_id' => $row['stock_id'], 
                    'shareholder_id' => $row['shareholder_id']
                ],
                [
                    'symbol' => $row['symbol'], 
                    'quantity' => $row['quantity'], 
                    'user_id' => $row['shareholder_id'],
                ]
            );
        }
    }
}
