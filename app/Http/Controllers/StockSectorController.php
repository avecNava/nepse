<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockSector;

class StockSectorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'admin']); 
    }

    /**
     * get sectors 
     */
    public function index()
    {
        $sectors = StockSector::OrderBy('sector','ASC')->get();
        return view('sector.sector', ['sectors' => $sectors]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'id' => 'nullable',
                'sector' => 'required|min:3',
                'sub_sector' => 'required|min:3',
            ]
        );
        StockSector::updateOrCreate(
            [ 'id'=> $validated['id'] ],            
            $validated
        );
        return redirect()->back()->with('message','✔ Sectors persisted', 201);
    }

    public function getSectorJSON(StockSector  $sector)
    {
        return $sector;
    }
}
