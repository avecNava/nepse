<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sales;
use App\Models\SalesBasket;
use App\Models\Portfolio;
use App\Models\PortfolioSummary;
use App\Models\Shareholder;
use App\Models\Stock;
use App\Models\Broker;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\UtilityService;
use App\Http\Requests\SalesRequest;
use Spatie\SimpleExcel\SimpleExcelWriter;

class SalesController extends Controller
{
    
    public function __construct()
    {
        $this->middleware(['auth', 'verified']); 
    }

    public function create()
    {
        $brokers = Broker::select('broker_no','broker_name')->orderBy('broker_no')->get();
        $stocks = Stock::select('id','symbol','security_name')->orderBy('symbol')->get();
        
        //get all the shareholder names to display in the select input
        $shareholders = Shareholder::where('parent_id', Auth::id())->orderBy('first_name')->get();

        return view('sales.new-sales',[
            'stocks' => $stocks,
            'brokers' => $brokers,
            'shareholders' => $shareholders,
            'notice' => UtilityService::getNotice(),
        ]);
    }

    public function store(SalesRequest $request)
    {
       
        $sales = new Sales();
        $sales->stock_id = $request->stock_id;
        $sales->shareholder_id = $request->shareholder_id;
        $sales->quantity = $request->quantity;
        $sales->wacc = $request->wacc;
        $sales->cost_price = $request->cost_price;
        $sales->sell_price = $request->sell_price;
        $sales->net_receivable = $request->net_receivable;
        $sales->sales_date = $request->sales_date;
        $sales->payment_date = $request->payment_date;
        $sales->broker_commission = $request->broker_commission;
        $sales->sebon_commission = $request->sebon_commission;
        $sales->capital_gain_tax = $request->capital_gain_tax;
        $sales->gain = $request->gain;
        $sales->dp_amount = $request->dp_amount;
        $sales->name_transfer = $request->name_transfer;
        $sales->receipt_number = $request->receipt_number;
        $sales->broker_no = $request->broker;
        $sales->remarks = $request->remarks;
        $sales->last_modified_by = Auth::id();
        $sales->save();

        $uuid = Shareholder::getShareholderUUID($request->shareholder_id);
        return redirect()->back()->with('message','New Sales record created successfully. Click <a target="_blank" rel="noopener noreferrer" href="'. url('sales', [$uuid]) . '">here</a> to view the record');
        
    }

    public function getSales(int $id)
    {
        $data = Sales::where('id', $id)->with(['share:id,symbol','shareholder:id,first_name,last_name'])->first();
     
        if(!empty($data)){
            return response()->json($data, 200);
        }
        
        return response()->json([
            'data' => null,
        ], 404);
    }

    public function view( $uuid = null )
    {
        
        $arr_shareholder_id = null; 
        $brokers = Broker::select('broker_no','broker_name')->orderBy('broker_no')->get();
        
        //if $uuid is null, get shareholders under current login        
        if(UtilityService::IsNullOrEmptyString($uuid)){
            
            $shareholders = Shareholder::getShareholderNames(Auth::id());
            
            //loop the shareholders and return comma separated ids
            $arr_shareholder_id = $shareholders->map(function($item){
                return ($item['id']);
            });
        }
        //otherwise, get id of the given $uuid
        else{
            $shareholder_id = Shareholder::where('uuid', $uuid)->pluck('id')->first();
            $arr_shareholder_id = [ $shareholder_id ]; 
        }


        //get sales for the given ids
        $sales = Sales::whereIn('shareholder_id', $arr_shareholder_id)
                ->with(['shareholder','share:id,symbol,security_name'])
                ->orderByDesc('sales_date')
                ->get();
        
        $shareholders = Shareholder::shareholdersWithSales(Auth::id());
        //loop the sales record and filter out shareholders (ie, shareholders with sales)
        // $grouped_shareholders = $sales->groupBy('shareholer_id')
        //     ->map(function($items, $key){
                
        //         //get unique shareholders
        //         $unique = $items->unique('shareholder_id');
                
        //         return $unique->map(function($row){

        //             $first_name = $row->shareholder->first_name;
        //             $last_name = $row->shareholder->last_name;
                    
        //             return [
        //                 'name' => "$first_name $last_name",
        //                 'relation' => $row->shareholder->relation,
        //                 'uuid' => $row->shareholder->uuid,
        //             ];
        //         });
        //     });
            
      
        return 
            view('sales.sales', 
            [
                'sales_grouped' => $sales->groupBy('shareholder_id'),
                'shareholders' => $shareholders,
                'selected' => Shareholder::getShareholderDetail($uuid),
                'brokers' => $brokers,
                'notice' => UtilityService::getNotice(),
            ]); 

    }

    public function update(SalesRequest $request)
    {
        // $validated = $request->validated();
        // dd($validated);
        
        $row_id = $request->get('id');
        $sales = Sales::find($request->id);
        if(!empty($sales)){
            $sales->stock_id = $request->stock_id;
            $sales->shareholder_id = $request->shareholder_id;
            $sales->quantity = $request->quantity;
            $sales->wacc = $request->wacc;
            $sales->cost_price = $request->cost_price;
            $sales->sell_price = $request->sell_price;
            $sales->net_receivable = $request->net_receivable;
            $sales->sales_date = $request->sales_date;
            $sales->payment_date = $request->payment_date;
            $sales->broker_commission = $request->broker_commission;
            $sales->sebon_commission = $request->sebon_commission;
            $sales->capital_gain_tax = $request->capital_gain_tax;
            $sales->gain = $request->gain;
            $sales->dp_amount = $request->dp_amount;
            $sales->name_transfer = $request->name_transfer;
            $sales->receipt_number = $request->receipt_number;
            $sales->broker_no = $request->broker;
            $sales->remarks = $request->remarks;
            $sales->last_modified_by = Auth::id();
            $sales->save();

            return redirect()->back()->with('message','Record updated successfully');
        } 

        else{
            return redirect()->back()->with('error','Record not found',404);
        }
    }
  
    //called when marked as Sold is called via Shopping basket
    public function markSold(Request $request)
    {
        //todo: update dp_amount for unique transactions shareholder_stock_day
       
        try {
            
            $error = false;
            $msg = null;
            $order = $request->quantity;

            // check if the stock has wacc updated in portfolio table (otherwise, don't add to sales, don't deduct from portfolio summary )
            $portfolio = Portfolio::where('shareholder_id', $request->shareholder_id)
                ->where('stock_id', $request->stock_id)
                ->whereNotNull('wacc_updated_at')
                ->get();
            
            if(empty($portfolio)){
                $message = 'Could not locate record'; $error = true;
            }

            $total = $portfolio->sum('quantity');
            if(!$error && $order > $total) {
                $msg = "Sell quantity '$order' exceeds the available quantity '$available_quantity'"; 
                $error = true;
            }elseif(!$error && $total == 0){
                $msg = "Total quantity available is 0. Did you update WACC for all the stocks?"; 
                $error = true;
            }
                
            //check if the sales is within limit
            // if(!$error &&  $order < config('app.buy-sell-limit')){
            //     $msg = 'Minimum buy sell limit is ' . config('app.buy-sell-limit');
            //     $error = true;
            // }
            
            if($error){
                return response()->json([
                    'status' => 'error',
                    'message' => $msg,
                    'row' => $request->record_id,
                ], 401);
            }
            
            Sales::create([
                'stock_id' => $request->stock_id,
                'portfolio_id' => $request->portfolio_id,
                'shareholder_id' => $request->shareholder_id,
                'quantity' => $request->quantity,
                'wacc' => $request->wacc,
                'sales_date' => Carbon::today(),
                'broker_commission' => $request->broker,
                'sebon_commission' => $request->sebon,
                'capital_gain_tax' => $request->cgt,
                'cost_price' => $request->cost_price,
                'sell_price' => $request->sell_price,
                'net_receivable' => $request->net_receivable,
                'last_modified_by' => Auth::id(),
            ]);
            
            //adjust portfolio quantity
            Portfolio::salesAdjustment($request->portfolio_id);

            //adjust portfolio summary with the sold quantities
            //NOTE: always do this after Portfolio is updated 
            PortfolioSummary::updateCascadePortfoliSummaries($request->shareholder_id,$request->stock_id);


            //remove from basket
            SalesBasket::destroy($request->record_id);
            
            return response()->json([
                'status' => 'success',
                'message' => "Selected record marked as sold.",
                'row' => $request->record_id,
            ], 201);

            
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error: '. $th->getMessage() . ' Line: ' . $th->getLine() . ' File: ' . $th->getFile(),
                // 'message' => 'An unexpected error occured. Please ensure that Effective rate is updated for the stock.',
            ], 500);
        }

    }

    public function export(Request $request)
    {
        $shareholder_id = Auth::id();
        
        $uuid = $request->shareholders;
        
        if(!empty($uuid)){
            $shareholder = Shareholder::getShareholderDetail($uuid);
            $shareholder_id = $shareholder->id;
        }

        $name = Auth::user()->name . '-' . $shareholder_id;
        $date = Carbon::now();
        $file_name = UtilityService::serializeString($name,'-') .'-'. $date->toDateString();
        
        $stocks = DB::table('sales as s')
            ->join('shareholders as m', function($join) use($shareholder_id){
                $join->on('m.id', '=', 's.shareholder_id')
                    ->where('m.id', $shareholder_id);
            })
            ->join('stocks as st', 'st.id', '=', 's.stock_id')
            ->select(
                's.*','st.symbol', 'm.first_name', 'm.last_name',
            )
            ->orderBy('st.symbol')
            ->get();

        try {
               
            $writer = SimpleExcelWriter::streamDownload("$file_name.xlsx");
            $stocks->map(function($item, $key) use($writer){
                $writer->addRow([
                    'SN' => $key + 1,
                    'Name' => $item->first_name . ' '. $item->last_name,
                    'Symbol' => $item->symbol,
                    'Quantity' => $item->quantity,
                    'WACC' => $item->wacc,
                    'Cost price' => $item->cost_price,
                    'Sell price' => $item->sell_price,
                    'Gain' => $item->gain,
                    'Broker commission' => $item->broker_commission,
                    'SEBON commission' => $item->sebon_commission,
                    'CGT' => $item->capital_gain_tax,
                    'DP Amount' => $item->dp_amount,
                    'Name transfer' => $item->name_transfer,
                    'Net receiveable' => $item->net_receivable,
                    'Receipt no' => $item->receipt_number,
                    'Remarks' => $item->remarks,
                    'Broker #' => $item->broker_no,
                    'Sales date' => $item->sales_date,
                    'Paid on' => $item->payment_date,
                ]);
            });

            $writer->toBrowser();            
            return redirect()->back()->with('message', "Sales record exported successfully");
        
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', "Sales record could not be exported");
        }

    }

}
