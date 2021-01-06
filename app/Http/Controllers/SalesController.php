<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sales;
use App\Models\SalesBasket;
use App\Models\Portfolio;
use App\Models\PortfolioSummary;
use App\Models\Shareholder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\UtilityService;

class SalesController extends Controller
{
    
    public function __construct()
    {
        $this->middleware(['auth', 'verified']); 
    }

    public function view($username, $id = null)
    {
        $shareholders = Shareholder::getShareholderNames(Auth::id());
        
        $user_ids = [ $id ];        
        if(empty($id)){
            $user_ids = $shareholders->map(function($item){
                return ($item['id']);
            });
        }
        
        
        $sales = Sales::whereIn('shareholder_id', $user_ids)
                ->with(['shareholder','share:id,symbol,security_name'])
                ->orderByDesc('sales_date')
                ->get();
        
        $grouped_shareholders = $sales->groupBy('shareholer_id')
            ->map(function($items, $key){
                
                //get unique shareholders
                $unique = $items->unique('shareholder_id');
                
                return $unique->map(function($row){

                    $first_name = $row->shareholder->first_name;
                    $last_name = $row->shareholder->last_name;
                    
                    return [
                        'username' => UtilityService::serializeNames($first_name, $last_name),
                        'name' => "$first_name $last_name",
                        'relation' => $row->shareholder->relation,
                        'id' => $row->shareholder->id,
                    ];
                });
            });
            
        return 
            view('sales.sales', 
            [
                'sales' => $sales,
                'shareholders' => $grouped_shareholders->first(),
            ]); 

    }

    //called when marked as Sold is called via Shopping basket
    public function store(Request $request)
    {
        //todo: update dp_amount for unique transactions shareholder_stock_day
       
        try {
            
            $error = false;
            $msg = null;
            $sell_quantity = $request->quantity;

            // check if the stock has wacc updated in portfolio table (otherwise, don't add to sales, don't deduct from portfolio summary )
            $available_quantity = Portfolio::where('shareholder_id', $request->shareholder_id)
                ->whereNotNull('wacc_updated_at')
                ->where('stock_id', $request->stock_id)
                ->sum('quantity');
                
            //check if sell_quantity > $available quantity
            if($sell_quantity > $available_quantity){
                $msg = "Sell quantity '$sell_quantity' exceeds the available quantity '$available_quantity'";
                $error = true;
            }

            if(!$error &&  $available_quantity <= 0){
                $msg = "The WACC for this stock needs to be updated before it could be sold";
                $error = true;
            }

            //check if the sales is within limit
            if(!$error &&  $sell_quantity < config('app.buy-sell-limit')){
                $msg = 'Minimum buy sell limit is ' . config('app.buy-sell-limit');
                $error = true;
            }
            
            if($error){
                return response()->json([
                    'status' => 'error',
                    'message' => $msg,
                    'row' => $request->record_id,
                ], 401);
            }

            DB::transaction(function() use($request){

                //record sales
                Sales::create([
                    'stock_id' => $request->stock_id,
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

                //remove from basket
                SalesBasket::destroy($request->record_id);
                
                //adjst portfolio summary with the sold quantities
                $new_quantity = PortfolioSummary::salesAdjustment(collect([
                        'stock_id' => $request->stock_id,
                        'shareholder_id' => $request->shareholder_id,
                        'quantity' => $request->quantity,
                ]));
                
            });

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

}
