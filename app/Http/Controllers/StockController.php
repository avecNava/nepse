<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\StockSector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'admin']); 
    }

    /**
     * get stocks 
     */
    public function index($sector = null)
    {
        $stocks = Stock::with(['sector:id,sector,sub_sector','user:id,name'])->OrderBy('symbol')->get();
        if($sector){
            session()->flash('sector_id', $sector);
            $stocks = $stocks->filter(function($item) use($sector){
                return $item->sector_id == $sector;
            });
        }
        $sectors = StockSector::OrderBy('sector','ASC')->get();
        return view('stock.stock', 
        [
            'stocks' => $stocks->sortBy(['sector_id','symbol']),
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
                'active' => 'nullable',
                'sector_id' => 'required'
            ],
            //customize sector_id to sector in the message
            $messages = [
                'sector_id.required' => 'Please choose a sector from the list',
            ]
        );

        //checkbox for acive is "on" so update to 1
        $validated['active'] = $request->active ? 1 : 0;
        $validated['last_modified_by'] = Auth::id();

        Stock::updateOrCreate(
            [ 'id'=> $validated['id'] ],            
            $validated
        );
        return redirect()->route('stocks')->with('message','✔ Stock persisted', 201);
    }
}
