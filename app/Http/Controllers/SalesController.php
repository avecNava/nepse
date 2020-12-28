<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sales;
use App\Models\SalesBasket;
use App\Models\PortfolioSummary;
use App\Models\Shareholder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{

    public function addToBasket()
    {
        
    }

    public function viewBasket()
    {
        $shareholders = Shareholder::getShareholderIds(Auth::id());

        $basket = SalesBasket::whereIn('shareholder_id', $shareholders )
            ->with(['share','shareholder'])
            ->orderByDesc('basket_date')
            ->get();
        
        return view('cart.view',[
                'basket' => $basket,
            ]
        ); 

    }

    public function storeToBasket(Request $request)
    {
        try {

            $shareholder = $request->shareholder_id;
            $stock = $request->stock_id;

            $quantity = $request->quantity;
            
            //check existing quantity
            $total_quantity =  PortfolioSummary::where(function($q) use($shareholder, $stock){
                                return $q->where('shareholder_id', $shareholder)
                                    ->where('stock_id', $stock);
                                })
                                ->sum('quantity');
            
            $wacc =  PortfolioSummary::where(function($q) use($shareholder, $stock){
                                return $q->where('shareholder_id', $shareholder)
                                    ->where('stock_id', $stock);
                                })
                                ->average('wacc');
            
            $existing_basket_quantity = SalesBasket::where(function($q) use($shareholder, $stock){
                                return $q->where('shareholder_id', $shareholder)
                                    ->where('stock_id', $stock);
                                })
                                ->sum('quantity');
            
            $new_quantity = $existing_basket_quantity + $quantity;
            $sales_amount =  round($wacc * $quantity, 2);

            $exceeded = false;
            //check if existing  basket quantity and current quantity exceeds the total quantity
            if($new_quantity > $total_quantity){
                $new_quantity = $existing_basket_quantity;
                $exceeded = true;                
            }

            //update or create the basket
            SalesBasket::updateOrCreate(
                [
                    'stock_id' => $request->stock_id,
                    'shareholder_id' => $request->shareholder_id,
                ],
                [
                    'quantity' => $new_quantity,
                    'wacc' => $wacc,
                    'sales_amount' => $sales_amount,
                    'last_modified_by' => Auth::id(),
                    'basket_date' => Carbon::now(),
                ]
            );

            $message = "$quantity units added to the basket. <div class='basket_total'>Basket total : $new_quantity</div>";
            if($exceeded){
                $message = "Sum of current and existing quantites in the basket exceeds total quantity. Cart quantity updated to : $new_quantity";  
            }
            
            
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
        ], 201);
    }

    public function view()
    {
        
    }

    public function store(Request $request)
    {
        # code...
    }
}
