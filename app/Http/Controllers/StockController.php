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
}
