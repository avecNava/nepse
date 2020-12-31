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

    public function view()
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

    //called when marked as Sold is called via Shopping basket
    public function store(Request $request)
    {
        //todo: update dp_amount for unique transactions shareholder_stock_day
        //todo: deduct sold qty from portfolio summary
        //todo: add a flag in portfolio table called wacc_updated (to only update such records into the portfolio summary during CRUD)
        try {
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

                SalesBasket::destroy($request->record_id);

                //update cascade portfolio summary (only records with updated_wacc)
            
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Record marked as sold',
            'row' => $request->record_id,
        ], 201);
    }

}
