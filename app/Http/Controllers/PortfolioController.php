<?php

namespace App\Http\Controllers;

use App\Models\MeroShare;
use App\Models\Shareholder;
use App\Models\Stock;
use App\Models\Sales;
use App\Models\StockSector;
use App\Models\Broker;
use App\Models\StockPrice;
use App\Models\StockOffering;
use App\Models\Portfolio;
use App\Models\PortfolioSummary;
use Illuminate\Support\Str;
use App\Http\Requests\PortfolioRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Services\UtilityService;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\AppLog;

class PortfolioController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']); 
    }

    public function Portfolio($uuid)
    {

        $transaction_date = StockPrice::getLastTransactionDate();

        $shareholders = Shareholder::shareholdersWithPortfolio($uuid, TRUE);
       
        $shareholder_id = Shareholder::where('uuid', $uuid)->pluck('id')->first();

        $stocks = DB::table('portfolio_summaries as p')
            ->join('shareholders as m', function($join) use($shareholder_id){
                $join->on('m.id', '=', 'p.shareholder_id')
                    ->where('m.id', $shareholder_id)
                    ->where('m.tenant_id', session()->get('tenant_id'));
            })
            ->join('stocks as s', 's.id', '=', 'p.stock_id')
            ->leftJoin('stock_prices as pr', function($join){
                $join->on('pr.stock_id','p.stock_id')
                    ->where('pr.latest',TRUE);
            })
            ->select(
                'p.*','s.*', 'm.*',
                'pr.close_price', 'pr.last_updated_price', 'pr.previous_day_close_price',
            )
            ->orderBy('s.symbol')
            ->get();
            
            //todo: sort records by worth
            
            //filter out stocks whose quantity = 0
            $stocks = $stocks->filter(function ($value, $key) {
                return ($value->quantity > 0);
            });
            
            $row = $stocks->first();
            $scrips =  $stocks->count('stock_id');
            $quantity =  $stocks->sum('quantity');
            $investment =  $stocks->sum(function($item){
                return $item->quantity * $item->wacc;
            });
            $worth = $stocks->sum(function($item){
                $rate = $item->last_updated_price ?  $item->last_updated_price : $item->close_price;
                return $item->quantity * $rate;
            });
            $prev_worth = $stocks->sum(function($item){
                $rate = $item->previous_day_close_price;
                return $item->quantity * $rate;
            });
            $gain = $worth - $investment;
            $change = $worth - $prev_worth;
           
            $data = [
                'scrips'  => $scrips,
                'quantity'  => $quantity,
                'investment'  => $investment,
                'worth'  => $worth,
                'prev_worth'  => $prev_worth,
                'gain' => $gain,
                'change' => $change,
                'gain_per' => UtilityService::calculatePercentage($gain, $investment),
                'gain_class' => UtilityService::gainLossClass($gain),
                'change_per' => UtilityService::calculatePercentage($change, $prev_worth),
                'change_class' => UtilityService::gainLossClass($change),
            ];

            return  view(
                'portfolio.portfolio', 
                [
                    'uuid' => $uuid,
                    'first_name' => optional($row)->first_name,
                    'shareholder' => optional($row)->first_name . " " . optional($row)->last_name,
                    'portfolios' => $stocks,
                    'scorecard' => $data,
                    'shareholders' => $shareholders,
                    'notice' => UtilityService::getNotice(),
                    'transaction_date' => Carbon::parse($transaction_date),
                ]
            );
        }

    /**
     * Calculates Broker commission
     * input: transaction amount
     * return : broker percentage (JSON)
     */
    public function commission(UtilityService $broker, $amount=0)
    {
        if($amount < 1){
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

        // $sectors = StockSector::all()->sortBy('sector');
        // $sectors = Stock::all()->sortBy('symbol');

        $offers = StockOffering::all()->sortBy('offer_code');
        
        // $brokers = Broker::all()->sortBy('broker_name');
        $brokers = Broker::select('broker_no','broker_name')->orderBy('broker_no')->get();
        
        $stocks = Stock::all()->sortBy('symbol');

        return  view('portfolio.portfolio-new',
        [
            // 'sectors' => $sectors,
            'offers' => $offers,
            'brokers' => $brokers,
            'stocks' => $stocks,
            'shareholders' => $shareholders,
            // 'stocks' => $stocks,
        ]);
    }

    /**
     * store portfolio (main form)
     */
    public function store(PortfolioRequest $request)
    {   
        $user_id = Auth::id();
        $uuid = Shareholder::getShareholderUUID($request->shareholder);
   
        try {
            
            Portfolio::createPortfolio($request);
        
            //todo: get $shareholder and stock id from db
            $shareholder = $request->shareholder;
            $stock = $request->stock;

            PortfolioSummary::updateCascadePortfoliSummaries($shareholder, $stock);

            return  redirect()->back()->with('message',"Record created successfully 👌.  <a href='" . url('portfolio', [$uuid]) ."'>View record</a>");

        } catch (\Throwable $th) {
            UtilityService::createLog('storePortfolio', $th);
            return  redirect()->back()->with('error', $th->getMessage());
        }
        
    }

    /**
     * getPortfolioDetail : gets the portfolio detail from the given id
     * input : record_id
     * output: json with portfolio detail
     */
    public function getPortfolioByID(int $id)
    {
        if($id){
            
            $portfolio = Portfolio::find($id);
            
            return response()
                ->json($portfolio,200);             //200 OK
        }

        return response()
        ->json(
            [
                'message' => '`id` is required but not provided.',
                'status' => 'error',
            ], 
            401                 //401 unauthorized
        );            
    }

    /**
     * update portfolio
     * 
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
                // 'quantity' => 'required|numeric|gt:0', 
                'offer' => 'required|numeric|gt:0', 
                'quantity' => 'required|regex:/^[0-9]+$/',
                'unit_cost' => 'required|regex:/^\d{1,13}(\.\d{1,4})?$/',
                'total_amount' => 'required|regex:/^\d{1,13}(\.\d{1,4})?$/',
                'base_amount' => 'required|regex:/^\d{1,13}(\.\d{1,4})?$/',
                'effective_rate' => 'required|regex:/^\d{1,13}(\.\d{1,4})?$/',
                'purchase_date' => 'nullable|date',
            ],
            [ 'offer.*' => 'Please specify an offering type']
        );

        $result = Portfolio::updatePortfolio($request);

        //CALCULATE total_quantity and wacc_rate ; update in summary table        
        // $data = $portfolio = Portfolio::find($request->id);  
        // PortfolioSummary::updateCascadePortfoliSummaries($data->shareholder_id, $data->stock_id);

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
    public function delete(Request $request)
    {

        if( empty($request->rows) ){
            return response()->json([
                 'status' => 'error',
                 'message' => 'Confused 👀 Did you select any record at all?',
             ]);
       }

       // /trans_id is comma separated (eg, 1,2,3,4,5), explode into array 
       $arr_id = Str::of($request->rows)->explode(',');

        //convert the given list of ids into array, chunk into two arrays, 
        //mass delete the first chunk, manually delete the last element, so observe event occurs
        $rowid = $arr_id[0];

        if(count($arr_id) > 1){
            $chunk = array_chunk($arr_id->toArray(), count($arr_id) - 1);    //2nd array will have last element
            $arr1 = $chunk[0];
            $rowid = $chunk[1][0];                      //get the last row id
        }
       
        
        //obtain the shareholder and stock id to update records in portfolio summary
        $portfolio = Portfolio::find($rowid);
        
        if(empty($portfolio)){
            return response()->json(
                [
                    'message'=> 'Could not find record', 
                    'status'=>'error',     

                ], 404);
        }

        $shareholder_id = $portfolio->shareholder_id;
        $stock_id = $portfolio->stock_id;


        //step 1: delete records from Portfolio
        //1.1 delete the first array (mass delete)
        if(!empty($arr1)){
            Portfolio::whereIn('id', $arr1)->delete();     //mass deletes            
        }
     
        //1.2 delete the last row
        $deleted = Portfolio::destroy($rowid);
        
        $desc = [
            'user' => Auth::user()->name, 
            'shareholder' => Shareholder::getShareholderName($shareholder_id) . ' - ' . $shareholder_id,
            'stock' => Stock::getSymbol($stock_id),
            'quantity' => $portfolio->quantity,
        ];

        AppLog::createLog([
            'module' => 'PortfolioController',
            'title' => 'DELETE Stock',
            'desc' => json_encode($desc),            
        ]);

        //step 2 : CALCULATE total_quantity and wacc_rate ; update in summary table
        PortfolioSummary::updateCascadePortfoliSummaries($shareholder_id, $stock_id);
        
        
        if( $portfolio ){
            
            $remaining = Portfolio::where('shareholder_id', $portfolio->shareholder_id)->where('stock_id',$portfolio->stock_id)->sum('quantity');

            //remove from summaries where quantity is zero
            PortfolioSummary::where('quantity', '<=', 0)->delete();  //todo: do this via oberver later
            
            $message = "Portfolio deleted. Remaining units $remaining";
            return response()->json(
                [
                    'quantity'=>$remaining,
                    'message'=> $message, 
                    'status'=>'success',
                ], 
                201
            );

        }
        return response()->json(
            [
                'quantity'=> 0,
                'message'=> 'Nothing to delete', 
            ], 
            201
        );

    }

    /**
     * Function: showPortfolioDetails
     * display the details (history) of the given portfolio
     * $username is just a label kept for clarity via Route::pattern
     * $symbol is the stock symbol eg, CHCL
     * $shareholder_id is the shareholder_id
     * $all (include SALES record from sales table)
     */

    public function showPortfolioDetails($symbol, $stock_id, $uuid)
    {
        
        $shareholder_id = Shareholder::where('uuid', $uuid)->pluck('id')->first();
        $transaction_date = StockPrice::getLastTransactionDate( $stock_id );
        
        if(!$shareholder_id){
            return response()->json(['message'=>'Incorect id'], 501);
        }

        $offers = StockOffering::all()->sortBy('offer_code');

        $sales = Sales::where('shareholder_id', $shareholder_id)
                ->with(['share:id,symbol'])
                ->orderByDesc('sales_date')
                ->get();

        $sales = $sales->filter(function($item, $key) use($stock_id){
            return $item->share->id == $stock_id;
        });

        
        $stock_price = StockPrice::getPrice($stock_id);
        
        $brokers = Broker::select('broker_no','broker_name')->orderBy('broker_no')->get();

        //portfolio data (for the given stock)
        $portfolios = DB::table('portfolios as p')
        ->join('shareholders as sh', function($join) use($shareholder_id){
            $join->on('sh.id', '=', 'p.shareholder_id')
                ->where('sh.id', $shareholder_id)
                ->where('sh.tenant_id', session()->get('tenant_id'));
        })
        ->leftJoin('stock_prices as pr', function($join){
            $join->on('pr.stock_id','=', 'p.stock_id')
            ->where('pr.latest', true);
        })
        ->leftJoin('stock_offerings as o', 'o.id', '=', 'p.offer_id')
        ->join('stocks as s', 's.id', '=', 'p.stock_id')
        ->leftJoin('stock_sectors as ss','ss.id', '=', 's.sector_id')
        ->select('p.*',
                'pr.close_price','pr.previous_day_close_price', 'pr.last_updated_price',
                'ss.sector',
                's.symbol', 's.security_name', 
                'sh.first_name', 'sh.last_name', 'sh.relation', 'sh.uuid',
                'o.offer_code','o.offer_name'
                )
        ->where(function($query) use($stock_id){
            $query->where('s.id','=', $stock_id);
        })->orderBy('purchase_date', 'DESC')->get();

        //summary data to display in the top band
        $summary = $portfolios->filter(function($item){
            return $item->wacc_updated_at != null;
        });        
        $item = $summary->first();
        
        $metadata = collect([
            'total_scripts' => $summary->count('stock_id'),
            'quantity' => $summary->sum('quantity'),
            'investment' => $summary->sum(function($item){
                                $rate = $item->effective_rate ? $item->effective_rate : $item->unit_cost;
                                return $item->quantity * $rate;
                            }),
        ]);
       

        return view("portfolio.portfolio-details", 
        [
            'sales'  => $sales,
            'price'  => $stock_price,
            'info'  => $metadata,
            'portfolios' => $portfolios,
            'offers' => $offers,
            'brokers' => $brokers,
            'shareholder' => collect(['name' => Shareholder::getShareholderName($shareholder_id), 'uuid' => $uuid]),
            'stock' =>  Stock::getStockDetail($symbol),
            'transaction_date' => Carbon::parse($transaction_date),
        ]);

    }

    /**
     * returns stocks for the given user (user_id) in JSON format
     */
    public function getUserStocks($id)
    {
        
        // $stocks = Portfolio::where('shareholder_id', $id)->get();
        $stocks = DB::table('portfolio_summaries as p')
            ->join('shareholders as m', function($join) use($id){
                $join->on('m.id', '=', 'p.shareholder_id')
                    ->where('m.uuid', $id);
            })
            ->join('stocks as s', 's.id', '=', 'p.stock_id')
            ->leftJoin('stock_prices as pr', function($join){
                $join->on('pr.stock_id','p.stock_id')
                    ->where('pr.latest',TRUE);
            })
            ->select(
                'p.*','s.*', 
                'pr.close_price', 'pr.last_updated_price', 'pr.previous_day_close_price',
                'm.first_name','m.last_name','m.uuid',
            )
            ->orderBy('s.symbol')
            ->get();
        
        if(!empty($stocks)){
            return  response()->json([
               'status' => 'success',
               'data' => $stocks->toJson(),
            ]);
        }
        return response()->json([
            'message' => 'Can not find any stocks with the supplied id',
            'status' => 'error',
        ]);
    }

    public function export(Request $request)
    {
        $shareholder_id = $request->id;
        $name = Auth::user()->name;
        $date = Carbon::now();
        $file_name = UtilityService::serializeString($name,'-') .'-'. $date->toDateString();
        
        $stocks = DB::table('portfolio_summaries as p')
            ->join('shareholders as m', function($join) use($shareholder_id){
                $join->on('m.id', '=', 'p.shareholder_id')
                    ->where('m.id', $shareholder_id);
            })
            ->join('stocks as s', 's.id', '=', 'p.stock_id')
            ->leftJoin('stock_prices as pr', function($join){
                $join->on('pr.stock_id','p.stock_id')
                    ->where('pr.latest',TRUE);
            })
            ->select(
                'p.*','s.*', 'm.*',
                'pr.close_price', 'pr.last_updated_price', 'pr.previous_day_close_price',
            )
            ->orderBy('s.symbol')
            ->get();

        try {
               
            $writer = SimpleExcelWriter::streamDownload("$file_name.xlsx");
            $stocks->map(function($item, $key) use($writer){
                $writer->addRow([
                    'SN' => $key + 1,
                    'Name' => $item->first_name . ' '. $item->last_name,
                    'Symbol' => $item->symbol,
                    'Quantity' => $item->quantity,
                    'Effective_rate' => $item->wacc,
                    'Cost' => $item->investment,
                ]);
            });

            $writer->toBrowser();            
            return redirect()->back()->with('message', "Portfolio exported successfully");
        
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', "Portfolio could not be exported");
        }

    }

}
