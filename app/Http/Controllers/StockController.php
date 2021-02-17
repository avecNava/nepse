<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\StockSector;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'admin']); 
    }

    public function index()
    {
        $stocks = Stock::with(['sector:id,sector'])->OrderByDesc('created_at')->get();
        $sectors = StockSector::OrderBy('sector','ASC')->get();
        return view('stock.stock', 
        [
            'stocks' => $stocks,
            'sectors' => $sectors,
        ]);
    }

    public function getStockJSON(Stock  $stock)
    {
        return $stock;
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'id' => 'nullable',
                'symbol' => 'required|min:3',
                'security_name' => 'required|min:3',
                'active' => 'required',
                'sector_id' => 'required'
            ],
            //customize sector_id to sector in the message
            $messages = [
                'sector_id.required' => 'Please choose a sector from the list',
            ]
        );

        //checkbox for acive is "on" so update to 1
        if($request->active){
            $validated['active'] = 1;
        }

        Stock::updateOrCreate(
            [ 'id'=> $validated['id'] ],            
            $validated
        );
        return redirect()->route('stocks')->with('message','✔ Stock persisted', 200);
    }
}
