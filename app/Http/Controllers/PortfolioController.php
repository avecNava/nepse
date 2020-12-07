<?php

namespace App\Http\Controllers;

use App\Models\MeroShare;
use App\Models\Shareholder;
use App\Models\Stock;
use App\Models\StockCategory;
use App\Models\StockOffer;
use App\Models\Portfolio;
use App\Models\PortfolioSummary;
use Illuminate\Support\Str;
use App\Http\Requests\StorePortfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Services\UtilityService;

class PortfolioController extends Controller
{

    public function __constructor()
    {
        $this->middleware('auth');
    }

    public function commission(UtilityService $broker, $amount=0)
    {
        if($amount<1){
            return response()->json([
                'amount' => $amount,
                'message' => 'Invalid amount',
            ]);
        }

        $comm = $broker->commission();
        $result = $comm->filter(function($item, $key) use($amount){
            return $amount >= $item['amount_fl'] && $amount <= $item['amount'];
        });
        
        $result = $result->first();
        return response()->json([
            'rate' => $result['broker'],
            'alias' => $result['alias'],
            'cap_amount' => $result['amount'],
        ]);
    }
    /**
     * form for new Portfolio
     */
    public function create()
    {
        $user_id = Auth::id();
        $shareholders = Shareholder::where('parent_id', $user_id)->get();

        $sectors = StockCategory::all()->sortBy('sector');
        $sectors = Stock::all()->sortBy('symbol');

        $offers = StockOffer::all()->sortBy('offer_code');
        
        // $brokers = Broker::all()->sortBy('broker_name');
        $brokers = collect([
            ['broker_no'=>37, 'broker_name'=>'Swarnalaxmi Securities'],
            ['broker_no'=>34, 'broker_name'=>'Another Broker '],
            ]);
        
        $stocks = Stock::all()->sortBy('symbol');

        return  view('portfolio.portfolio-new',
        [
            'sectors' => $sectors,
            'offers' => $offers,
            'brokers' => $brokers,
            'stocks' => $stocks,
            'shareholders' => $shareholders,
            'stocks' => $stocks,
        ]);
    }

    /**
     * store portfolio (main form)
     */
    public function store(StorePortfolio $request)
    {   
        $user_id = Auth::id();

        // todo: update the portfolio_summary table
        Portfolio::createPortfolio($request);

        return  redirect()->back()->with('message','Record created successfully 👌 ');
        
    }

        /**
     * getPortfolioDetail : gets the portfolio detail from the given id
     * input : record_id
     * output: json with portfolio detail
     */
    public function getPortfolioByID(int $id)
    {
        if($id){
            
            $portfolio = Portfolio::where('id', $id)->first();
            return $portfolio->toJson();
        }

        return response()
        ->json([
            'message' => '`id` is required but not provided.',
            'status' => 'error',
        ]);
    }


    /**
     * update portfolio
     * 
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            // 'quantity' => 'required|numeric|gt:0', 
            'quantity' => 'required|regex:/^[1-9][0-9]+$/',
            'unit_cost' => 'required|regex:/^\d{1,13}(\.\d{1,4})?$/',
            'total_amount' => 'required|regex:/^\d{1,13}(\.\d{1,4})?$/',
            'effective_rate' => 'required|regex:/^\d{1,13}(\.\d{1,4})?$/',
            // 'effective_rate' => 'required|regex:/^[1-9][0-9]+$/',
        ]);

        //todo: update the portfolio_summary table
        Portfolio::updatePortfolio($request);
        
        return redirect()->back()->with('message', 'Record updated successfully 👌');

    }


    /**
     * delete the portfolio
     * $id is the record id
     */
    public function delete(int $id)
    {
                
        if(!$id){

            return response()->json(
                [
                    'action'=>'delete', 
                    'message'=> 'Shareholder id can not be null', 
                    'status'=>'error',                
                ]);
        }
        
        //todo: update the portfolio_summary table
        $deleted = Portfolio::destroy($id);
        
        if($deleted > 0){

            $message = "Portfolio deleted. Record id : $id";
        
            return response()->json(
                [
                    'action'=>'delete', 
                    'message'=> $message, 
                    'status'=>'success',
                ]);
        }

    }


    /**
     * Function: showPortfolioDetails
     * display the details (history) of the given portfolio
     * $username is just a label kept for clarity via Route::pattern
     * $symbol is the stock symbol eg, CHCL
     * $member is the shareholder_id
     */

    public function showPortfolioDetails($username, $symbol, $member)
    {
        //todo: check authorizations
        
        $user_id = Auth::id();      //find shareholder info when null

        // $sectors = StockCategory::all()->sortBy('sector');
        // $stocks = Stock::all()->sortBy('symbol');
        // $shareholders = Shareholder::where('parent_id', $user_id)->get()    ;       //only select shareholders for the current 
        
        $offers = StockOffer::all()->sortBy('offer_code');

        $portfolios = DB::table('portfolios as p')
        ->join('shareholders as sh', 'sh.id', '=', 'p.shareholder_id')
        ->leftJoin('stock_offers as o', 'o.id', '=', 'p.offer_id')
        ->join('stocks as s', 's.id', '=', 'p.stock_id')
        ->leftJoin('stock_sectors as ss','ss.id', '=', 's.sector_id')
        ->select('p.*',
                'ss.sector',
                's.symbol', 's.security_name', 
                'sh.first_name', 'sh.last_name','sh.relation',
                'o.offer_code','o.offer_name'
                )
        ->where(function($query) use($member, $symbol){
            $query->where('p.shareholder_id', $member)
            ->where('s.symbol','=', $symbol);
        })->orderBy('purchase_date', 'DESC')->get();
        
        //collect info (shareholder name, total shares, security_name)
        $metadata = collect([]);
        $temp = $portfolios->each(function($item, $key) use($metadata){
            $metadata->push([
                'quantity' => $item->quantity,
                'shareholder_id' => $item->shareholder_id,
                'symbol' => "$item->security_name ($item->symbol)",
                'shareholder' => $item->first_name . ' '. $item->last_name,
                'relation' => !empty($item->relation) ? " ($item->relation)" : '',
            ]);
        });

        $obj = $metadata->first();
        if(!empty($obj)){
            $symbol = $obj['symbol'];
            $shareholder_id = $obj['shareholder_id'];
            $shareholder = $obj['shareholder'] . $obj['relation'];
            $quantity = $metadata->sum('quantity');
        } else {
            $symbol = '';
            $shareholder_id = '';
            $shareholder = '';
            $quantity = '';
        }
        
        return view("portfolio.portfolio-details", 
            [
                'total_stocks'  => $quantity,
                'last_price'  => 0,
                'stock_name' => $symbol,
                'shareholder_id' => $shareholder_id,
                'shareholder_name' => $shareholder,
                'total_investment'  => 0,
                'net_gain' => 0,
                'net_worth' => 0,
                'portfolios' => $portfolios,
                'offers' => $offers,
                'brokers' => [],
            ]);

    }


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
                $net_quantity = $total_cr - $total_dr;
                $row = array(
                    'quantity' => $net_quantity,
                    'shareholder_id' => $value->shareholder_id,
                    'stock_id' =>  empty($value->share) ? null : $value->share->id,
                    'symbol' =>  empty($value->share) ? null : $value->share->symbol,
                );
                
                //only store stocks whose quantity > 0
                if($net_quantity > 0){
                    $portfolios_sum->push( $row );
                }
                
            }); //end of map

            // $portfolios->dd();
            // $portfolios_sum->dd();

            try {                
           
                //add or update the cleaned data to the protfolio table based on stock_id and shareholder-id
                //portfolio (all transactions)
                foreach ($portfolios as $row) {
                    //update record if the following five attributes are met,
                    //else not create a new record with the following attributes
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
                        'last_modified_by' => Auth::id(),
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
                            'last_modified_by' => $row['shareholder_id'],
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
                'message' => count($portfolios) . " records have been imported to your poftfolio 👌",
                'count' => count($portfolios),
            ]);

        } //end if

        return response()->json([
            'status' => 'error',
            'message' => 'Confused 👀.Did you select any record at all?',
        ]);

    }   

}
