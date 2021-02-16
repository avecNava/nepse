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

    public function view($uuid = null)
    {
        $arr_shareholder_id = null; 
        
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

        $baskets = SalesBasket::whereIn('shareholder_id', $arr_shareholder_id )
            ->with(['share','shareholder:*','price:stock_id,close_price,last_updated_price'])
            ->orderByDesc('created_at')
            ->get();
        
        $shareholders = Shareholder::shareholdersWithCarts(Auth::id());
        
        return view('cart.cart',[
                'baskets' => $baskets,
                'shareholders' => $shareholders,
                'selected' => Shareholder::getShareholderDetail($uuid),
            ]
        ); 

    }

    public function store(Request $request)
    { 
        try {
            
            $error = false;
            $order = $request->quantity;
            $stock_id = $request->stock_id;
            $uuid = $request->uuid;
            $shareholder_id = Shareholder::where('uuid', $uuid)->pluck('id')->first();

            //check if the sales is within limit
            if( $order < config('app.buy-sell-limit')){
                return response()->json([
                    'status' => 'error','message' => 'Minimum sell limit is ' . config('app.buy-sell-limit'). ' units'], 401);
            }
            
            $portfolio  = Portfolio::where('shareholder_id', $shareholder_id)
                        ->where('stock_id', $stock_id)
                        ->whereNotNull('wacc_updated_at')
                        ->orderByDesc('purchase_date')
                        ->get();
            
            if(empty($portfolio)) {
                return response()->json(['status'=>'error','message' => 'Could not locate record'], 404) ;
            }
            
            $total = $portfolio->sum('quantity');
            $existing = SalesBasket::where(function($q) use($shareholder_id, $stock_id){
                return $q->where('shareholder_id', $shareholder_id)
                    ->where('stock_id', $stock_id);
                })
                ->sum('quantity');
            
            if(($order + $existing) > $total) {
                return response()->json(['message' => 'Order quantity exceeds total quantity. Please also check if this stock has already been added to the basket earlier'], 200) ;
            }
            
            $cart = collect();
            $diff = $order;                 //diff between order and total quantity (sum)

            //1. pick the exact quantity ordered (if available)
            foreach($portfolio as $item){
                if($order == $item->quantity){
                    $cart->push(['quantity' => $item->quantity, 'id' => $item->id]);
                    $diff = 0;                      //all orders satisfied 
                    break;
                }   
            }

            //if exact match is not found, loop and sum up individual quantities to match the order placed
            if(count($cart) < 1){           //$cart is an empty collection, so count will be 0
                
                //2. calculate denominations of quantities to meet the order placed
                $sum = 0;
                foreach($portfolio as $item){
                
                    if(($sum + $item->quantity) <= $order){
                        $sum +=  $item->quantity;
                        $diff = $order - $sum;
                        $cart->push(['quantity' => $item->quantity, 'id'=>$item->id]);          //denominations to satisfy the order
                    }
                }
            }
            
            //calculate wacc
            $wacc =  $portfolio->sum('effective_rate') / count($portfolio);
          
            //insert into salesbasket
            foreach ($cart as $item) {            
            
                //update or create the basket
                SalesBasket::updateOrCreate(
                    [
                        'portfolio_id' => $item['id'],
                        'stock_id' => $stock_id,
                        'shareholder_id' => $shareholder_id,
                    ],
                    [
                        'quantity' => $item['quantity'],
                        'wacc' => $wacc,
                        'sell_price' => round($wacc * $item['quantity'], 2),
                        'last_modified_by' => Auth::id(),
                        'basket_date' => Carbon::now(),
                    ]
                );

            }
            
            //loop the cart and collect the ids
            $arr_id = collect();
            foreach ($cart as $item){
                $arr_id->push($item['id']);
            }
            
            //the above operation may not fully satisfy the orders, so handle the diff if any
            if($diff > 0){
                
                //1. get portfolio that has not yet been added into the cart
                //https://laravel.com/docs/8.x/collections#method-wherenotin
                $record = $portfolio->whereNotIn('id', $arr_id->toArray());
                if(!empty($record)){

                    //2. add the diff to the cart, deduct the quantity in portfolio
                    $row = $record->first();
                    SalesBasket::updateOrCreate(
                    [
                        'portfolio_id' => $row->id,
                        'stock_id' => $row->stock_id,
                        'shareholder_id' => $shareholder_id,
                    ],
                    [
                        'quantity' => $diff,
                        'wacc' => $wacc,
                        'sell_price' => round($wacc * $diff, 2),
                        'last_modified_by' => Auth::id(),
                        'basket_date' => Carbon::now(),
                    ]);

                }

            }

            $message = "$order units added to the basket.";
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
