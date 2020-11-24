<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Relation;
use App\Models\Shareholder;
use Illuminate\Support\Facades\Auth;

class ShareholderController extends Controller
{
    public function __construct()
    {
        
    }
    
    public function index()
    {
        $shareholder_id = Auth::id();
        $relationships = Relation::orderBy('relation','asc')->get();
        //get all the shareholder names
        $shareholders = Shareholder::where('parent_id', $shareholder_id)
                        ->get();
        return view('shareholder',[
            'shareholders' => $shareholders,
            'relationships' => $relationships,
        ]);
    }
    
    public function create()
    {
        
    }
}
