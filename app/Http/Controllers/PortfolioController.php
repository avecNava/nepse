<?php

namespace App\Http\Controllers;

use App\Models\MeroShare;
use App\Models\Shareholder;
use App\Models\Stock;
use App\Models\StockCategory;
use App\Models\StockOffer;
use App\Models\Portfolio;
use App\Models\PortfolioSummary;
// use App\Models\StockPrice;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PortfolioController extends Controller
{
    
    public function __constructor()
    {
        
        // Auth::loginUsingId(1);        
        // Auth::loginUsingId(1, true);         // Login and "remember" the given user...
        $this->middleware('auth');
        
    }

    public function new()
    {
        return "new";
    }

    /**
     * display the portfolio summary
     */

    public function portfolioDetails($symbol = null, $shareholder = null)
    {
        //todo: check authorizations

        //find shareholder info when null
        $user_id = Auth::id();

        $shareholder_id = $shareholder;
        if(empty($shareholder_id)){
            $shareholder = Shareholder::where('parent_id', $user_id)->pluck('id')->all();
        }
        $categories = StockCategory::all()->sortBy('sector');
        $offers = StockOffer::all()->sortBy('offer_code');
        $stocks = Stock::all()->sortBy('symbol');
        $shareholders = Shareholder::where('parent_id', $user_id)->get()    ;       //only select shareholders for the current 
        // $transaction_date = StockPrice::getLastDate();
        $portfolios = DB::table('portfolios')
        ->join('stocks', 'stocks.id', '=', 'portfolios.stock_id')
        ->join('shareholders', 'shareholders.id', '=', 'portfolios.shareholder_id')
        ->join('stock_categories', 'stock_categories.id', '=', 'portfolios.category_id')
        ->join('stock_offers', 'stock_offers.id', '=', 'portfolios.offer_id')
        ->select('portfolios.*','stocks.security_name', 'shareholders.first_name','shareholders.last_name','stock_categories.sector','stock_offers.offer_code')            ->get();
        $portfolios = $portfolios->sortByDesc('purchase_date');
        $portfolios->dd();
        
        return view("portfolio-details", 
        [
            'portfolios' => $portfolios,
            'shareholders' => $shareholders,
            'shareholder_id' => empty($shareholder_id) ? 0 : $shareholder_id,
            'categories' => $categories,
            'offers' => $offers,
            'stocks' => $stocks,
            'transaction_date' => $transaction_date,
        ]);

    }

   
    // public function index($shareholder_id = null)
    // {
    //     //if shareholder_id is null, get "ALL Portfolio" [current user and all shareholders under the current user]
    //     //else load the portfolio for the given shareholder_id
    //     $user_id = Auth::id();
    //     $shareholder = $shareholder_id;
    //     if(empty($shareholder_id)){
    //         $shareholder = Shareholder::where('parent_id', $user_id)->pluck('id')->all();
    //     }

    //     $shareholders = Shareholder::where('parent_id', $user_id)->get()    ;       
    //     $transaction_date = StockPrice::getLastDate();
    //     // $portfolios = Portfolio::where('shareholder_id', $shareholder)
    //     //                 ->with(['shareholder','share','stockPrice'=>function($q) use($transaction_date) {
    //     //                     $q->where('transaction_date', '>=', $transaction_date);
    //     //                   }])->get();
    //     // $portfolios = Portfolio::where('shareholder_id', $shareholder)
    //     //                 ->with(['shareholder','price','share'])
    //     //                 ->get();

    //     $portfolios = DB::table('portfolios')
    //     ->join('stocks', 'stocks.id', '=', 'portfolios.stock_id')
    //     ->join('shareholders', 'shareholders.id', '=', 'portfolios.shareholder_id')
    //     ->join('stock_prices', 'stock_prices.stock_id', '=', 'portfolios.stock_id')
    //     ->select('portfolios.*','stocks.*', 'shareholders.*','stock_prices.*')
    //     ->where('stock_prices.transaction_date','=',$transaction_date)
    //     ->get();
    //     $portfolios = $portfolios->sortByDesc('quantity');
    
        
    //     return view("portfolio", 
    //         [
    //             'portfolios' => $portfolios,
    //             'shareholders' => $shareholders,
    //             'shareholder_id' => empty($shareholder_id) ? 0 : $shareholder_id,
    //             'transaction_date' => $transaction_date,
    //         ]
    //     );
        
    // }
    
    
    // /**
    //  * reads data from MeroShare table (meroshare_transactions) along with related data from Shares table
    //  * and forms array object
    //  * http://dev.nepse/meroshare/import-transaction
    //  * NOTE: this function is for testing purpose only and not used in PRODUCTION
    //  * The same logic is applied in storeToPortfolio() function and used in PRODUCTION
    //  */
    // public function portfolio()
    // {   
    //     $user_id = 2;
    //     $total_dr = 0;
    //     $total_cr = 0;
    //     $portfolios = collect([]);

    //     // $transactions = MeroShare::join('stocks', 'stocks.symbol', '=', 'meroshare_transactions.symbol')
    //     //     ->get(['meroshare_transactions.*','stocks.id']);

    //     //https://laravel.com/docs/8.x/eloquent-relationships#constraining-eager-loads
    //     // $transactions = MeroShare::with(['share' => function ($query) {
    //     //     $query->where('title', 'like', '%first%');
    //     // }])->get();
        
    //     $transactions = MeroShare::where('shareholder_id', $user_id)->with('share:id,symbol,security_name')->get();
      
        
    //     $temp = $transactions->groupBy('symbol');
    //     $temp->map(function($item) use($portfolios, $total_cr, $total_dr){
            
    //         foreach ($item as $value) {
                
    //             $total_cr += empty($value->credit_quantity) ? 0 : $value->credit_quantity;
    //             $total_dr += empty($value->debit_quantity) ? 0 : $value->debit_quantity;
                
    //             //combine data from main and related table and bind add to collection
    //             $portfolio = array(
    //                 'id' => $value->id,
    //                 'symbol' => $value->symbol,
    //                 'stock_id' => $value->id,
    //                 'quantity' => $total_cr - $total_dr,
    //                 'user_id' => $value->shareholder_id,
    //                 'shareholder_id' => $value->shareholder_id,
    //                 'security_name' => empty($value->share) ? null :  $value->share->security_name,
    //                 'stock_id' =>  empty($value->share) ? null : $value->share->id,
    //             );

    //             $portfolios->push( $portfolio );
                
    //         };
    //     });

    //     // $portfolios->dd();
        
    //     //add or update the cleaned data to the protfolio table based on stock_id and shareholder-id
    //     //one shareholder = one unique stock
    //     foreach ($portfolios as $row) {
    //         Portfolio::updateOrCreate(
    //             [
    //                 'stock_id' => $row['stock_id'], 
    //                 'shareholder_id' => $row['shareholder_id']
    //             ],
    //             [
    //                 'symbol' => $row['symbol'], 
    //                 'quantity' => $row['quantity'], 
    //                 'user_id' => $row['shareholder_id'],
    //             ]
    //         );
    //     }
    // }

    /**
     * This function is called via AJAX POST method 
     * when "Import to Poftfolio" is clicked on http://dev.nepse/meroshare/transaction route
     * Input parameters (Request object with trans_ids and shareholder_id)
     * The trans_ids and related data are stored into the Portfolio table for the given shareholder_id
     * 
     */
    public function storeToPortfolio(Request $request)
    {
        $user_id = Auth::id();
        
        $portfolios_sum  = collect([]);                         //data for portfolio_summary table
        $portfolios = collect([]);                               //data for portfolio table

        if( !empty($request->trans_id) ){

           //trans_id is comma separated (eg, 1,2,3,4,5), explode into array 
            $ids = Str::of($request->trans_id)->explode(',');
            
            // Get portfolio from meroshare_transactions table, related data from Shares and Offers table 
            //consturct collections to store data
            $transactions = 
            MeroShare::whereIn('id', $ids->toArray())
                    ->with(['share:id,symbol','offer:id,offer_code'])         //relationships (share, offer)
                    ->get();
            
            //group by data by symbol; loop each symbol group; aggregate the debit and credit quantities
            $transactions = $transactions->groupBy('symbol');
            $transactions->map(function($item) use($portfolios, $portfolios_sum){

                $total_dr = 0;
                $total_cr = 0;

                foreach ($item as $value) {
                    
                    //add up the total debit and credit for each symbols within a group
                    $total_cr += empty($value->credit_quantity) ? 0 : $value->credit_quantity;
                    $total_dr += empty($value->debit_quantity) ? 0 : $value->debit_quantity;
                    
                    $row = array(
                        'quantity' => empty($value->credit_quantity) ? $value->debit_quantity : $value->credit_quantity,
                        'shareholder_id' => $value->shareholder_id,
                        'symbol' =>  empty($value->share) ? null : $value->share->symbol,
                        'stock_id' =>  empty($value->share) ? null : $value->share->id,
                        'offer_id' =>  empty($value->offer) ? null : $value->offer->id,            //get from related table
                        'offer_code' =>  empty($value->offer) ? null : $value->offer->offer_code,            //get from related table
                        'purchase_date' => empty($value->credit_quantity) ? null : $value->transaction_date,
                        'sales_date'    => empty($value->debit_quantity) ? null :  $value->transaction_date,
                        'remarks' => $value->remarks,
                    );    
                    $portfolios->push( $row );
                    
                };

                //aggregated (summarized) portfolio
                $row = array(
                    'quantity' => $total_cr - $total_dr,
                    'shareholder_id' => $value->shareholder_id,
                    'stock_id' =>  empty($value->share) ? null : $value->share->id,
                    'symbol' =>  empty($value->share) ? null : $value->share->symbol,
                );
                $portfolios_sum->push( $row );
                
            }); //end of map

            // $portfolios->dd();
            // $portfolios_sum->dd();

            try {                
           
                //add or update the cleaned data to the protfolio table based on stock_id and shareholder-id
                //portfolio (all transactions)
                foreach ($portfolios as $row) {
                    Portfolio::updateOrCreate(
                    [
                        'stock_id' => $row['stock_id'], 
                        'shareholder_id' => $row['shareholder_id'],
                        'offer_id' => $row['offer_id'],
                        'quantity' => $row['quantity'], 
                        'purchase_date' => $row['purchase_date'],
                    ],
                    [
                        'offer_id' => $row['offer_id'],
                        'last_updated_by' => $row['shareholder_id'],
                        'sales_date' => $row['sales_date'],
                        'remarks' => $row['remarks'],
                    ]);
                }
                    
                //portfolio_summary
                foreach ($portfolios_sum as $row) {
                    PortfolioSummary::updateOrCreate(
                        [
                            'stock_id' => $row['stock_id'], 
                            'shareholder_id' => $row['shareholder_id'],
                        ],
                        [
                            'quantity' => $row['quantity'], 
                            'last_updated_by' => $row['shareholder_id'],
                        ]
                    );
                }

            } catch (QueryException $exception) {
                return response()->json([
                    'status' => 'error',
                    'message' => $exception->getMessage(),
                ]);
            }
            return response()->json([
                'status' => 'success',
                'transaction_id' => $portfolios,
            ]);

        } //end if

        return response()->json([
            'status' => 'error',
            'message' => '😉 No data received. Did you select any record?',
        ]);

    }   
}
