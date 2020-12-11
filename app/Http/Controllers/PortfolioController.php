<?php

namespace App\Http\Controllers;

use App\Models\MeroShare;
use App\Models\Shareholder;
use App\Models\Stock;
use App\Models\StockSector;
use App\Models\StockOffering;
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

    /**
     * Calculates Broker commission
     * input: transaction amount
     * return : broker percentage (JSON)
     */
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
            return $amount >= $item['min_amount'] && $amount <= $item['max_amount'];
        });
        
        $result = $result->first();
        return response()->json([
            'broker' => $result['broker'],
            'sebon' => $result['sebon'],
            'label' => $result['label'],
            'amount' => $result['max_amount'],
        ]);
    }
    /**
     * form for new Portfolio
     */
    public function create()
    {
        $user_id = Auth::id();
        $shareholders = Shareholder::where('parent_id', $user_id)->get();

        $sectors = StockSector::all()->sortBy('sector');
        $sectors = Stock::all()->sortBy('symbol');

        $offers = StockOffering::all()->sortBy('offer_code');
        
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
            'quantity' => 'required|regex:/^[0-9]+$/',
            'unit_cost' => 'required|regex:/^\d{1,13}(\.\d{1,4})?$/',
            'total_amount' => 'required|regex:/^\d{1,13}(\.\d{1,4})?$/',
            'effective_rate' => 'required|regex:/^\d{1,13}(\.\d{1,4})?$/',
            // 'effective_rate' => 'required|regex:/^[1-9][0-9]+$/',
        ]);

        $result = Portfolio::updatePortfolio($request);

        $result = $result->getData();
        
        if($result->status=='success'){

            return redirect()->back()->with('message', $result->message );
        }
        else{
            return redirect()->back()->withErrors($result->message );

        }


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
        
        $portfolio = Portfolio::where('id', $id)->first();
        $deleted = Portfolio::destroy($id);

        //CALCULATE total_quantity and wacc_rate ; update in summary table
        if(!empty($portfolio)){
            PortfolioSummary::updateCascadePortfoliSummaries($portfolio->shareholder_id, $portfolio->stock_id);
        }
        
        
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

        // $sectors = StockSector::all()->sortBy('sector');
        // $stocks = Stock::all()->sortBy('symbol');
        // $shareholders = Shareholder::where('parent_id', $user_id)->get()    ;       //only select shareholders for the current 
        
        $offers = StockOffering::all()->sortBy('offer_code');

        $portfolios = DB::table('portfolios as p')
        ->join('shareholders as sh', 'sh.id', '=', 'p.shareholder_id')
        ->leftJoin('stock_offerings as o', 'o.id', '=', 'p.offer_id')
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

}
