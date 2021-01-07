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
use Illuminate\Support\Str;
use App\Services\UtilityService;

class SalesBasketController extends Controller
{
    protected $dp = 25;
    
    public function __construct()
    {
        $this->middleware(['auth', 'verified']); 
    }

    public function create()
    {
        
    }

    public function view($username, $id = null)
    {
        // $shareholder_ids = Shareholder::getShareholderNames(Auth::id());
        
        $ids = [$id];
        
        if(empty($id))
            $ids = Shareholder::getShareholderIds(Auth::id());

        $baskets = SalesBasket::whereIn('shareholder_id', $ids )
            ->with(['share','shareholder:*','price:stock_id,close_price,last_updated_price'])
            ->orderByDesc('created_at')
            ->get();
        
        $grouped_shareholders = $baskets->groupBy('shareholer_id')
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
        
        // $grouped = $basket->groupBy('shareholder_id');

        return view('cart.cart',[
                'baskets' => $baskets,
                'shareholders' => $grouped_shareholders->first(),
            ]
        ); 

    }

    public function store(Request $request)
    {
        try {
            
            $error = false;
            $stock = $request->stock_id;
            $basket_quantity = $request->quantity;
            $uuid = $request->uuid;
            $shareholder_id = Shareholder::where('uuid', $uuid)->pluck('id')->first();

            //check if the sales is within limit
            if( $basket_quantity < config('app.buy-sell-limit')){
                $msg = 'Minimum sell limit is ' . config('app.buy-sell-limit'). ' units';
                $error = true;
            }
            
            if(! $error ) {

                // check if the stock has wacc updated in portfolio table, stocks without wacc can't be sold
                //when wacc is updated, it'll update the portfolio summary table too
                $available_quantity = Portfolio::where('shareholder_id', $shareholder_id)
                    ->whereNotNull('wacc_updated_at')
                    ->where('stock_id', $stock)
                    ->sum('quantity');
                if($available_quantity < $basket_quantity){
                    $error = true;
                    $msg = 'Some of the stocks have not been updated. Stocks marked <sup>*</sup> needs to be updated.';
                }
            }

            if(! $error ) {
                //get wacc from portfolio summary
                $wacc =  PortfolioSummary::where(function($q) use($shareholder_id, $stock){
                        return $q->where('shareholder_id', $shareholder_id)
                            ->where('stock_id', $stock);
                        })
                        ->average('wacc');
                
                $existing_basket_quantity = SalesBasket::where(function($q) use($shareholder_id, $stock){
                        return $q->where('shareholder_id', $shareholder_id)
                            ->where('stock_id', $stock);
                        })
                        ->sum('quantity');
                
                $new_quantity = $existing_basket_quantity + $basket_quantity;
                $sell_price =  round($wacc * $basket_quantity, 2);

                //check if existing  basket quantity and current quantity exceeds the total quantity
                if($new_quantity > $available_quantity){
                    $msg = "Sum of current and existing quantity in the basket exceeds total";
                    $error = true;
                }
            }
            
            //todo: update status code
            if($error){
                return response()->json([
                    'status' => 'error',
                    'message' => $msg,
                ], 401);
            }
            
            //update or create the basket
            SalesBasket::updateOrCreate(
                [
                    'stock_id' => $request->stock_id,
                    'shareholder_id' => $shareholder_id,
                ],
                [
                    'quantity' => $new_quantity,
                    'wacc' => $wacc,
                    'sell_price' => $sell_price,
                    'last_modified_by' => Auth::id(),
                    'basket_date' => Carbon::now(),
                ]
            );

            $message = "$basket_quantity units added to the basket. 
                        <span class='basket_total'>Basket total : $new_quantity </span>";
                return response()->json([
                'status' => 'success',
                'message' => $message,
            ], 201);
            
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error: '. $th->getMessage() . ' Line: ' . $th->getLine() . ' File: ' . $th->getFile(),
            ], 500);
        }

        
    }

    public function update(Request $request)
    {
        $record = SalesBasket::find($request->record_id);
        $record->stock_id = $request->stock_id;
        $record->shareholder_id = $request->shareholder_id;
        $record->quantity = $request->quantity;
        $record->wacc = $request->wacc;
        $record->broker_commission = $request->broker;                                
        $record->sebon_commission = $request->sebon;                                        
        $record->capital_gain_tax = $request->cgt;
        $record->dp_amount =$this->dp;
        $record->net_receivable = $request->net_receivable - $this->dp;
        $record->sell_price = $request->sell_price;
        $record->cost_price = $request->cost_price;
        $record->last_modified_by = Auth::id();
        $record->basket_date = Carbon::now();
        $record->save();

        return response()->json([
            'message' => 'Cart updated',
        ], 201);
    }

    public function delete(Request $request)
    {   
        
        if( empty($request->ids) ){

            return response()->json([
                 'status' => 'error',
                 'message' => 'Confused 👀 Did you select any record at all?',
             ]);

       }

       // /id is comma separated (eg, 1,2,3,4,5), explode into array 
       $ids = Str::of($request->ids)->explode(',');
       
       $count  =  SalesBasket::whereIn('id', $ids->toArray())->delete();
       $records = $count > 1 ? ' records' : ' record';
       return response()->json([
            'message' => "$count $records deleted. Refreshing the page ⌚ . . .",
            'count' => $count,
       ]);
    }

}
