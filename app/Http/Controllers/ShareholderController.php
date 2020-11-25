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
        $user_id = Auth::id();
        $relationships = Relation::orderBy('relation','asc')->get();
        //get all the shareholder names
        // $shareholders = Shareholder::where('parent_id', $user_id)->get();
        $shareholders = Shareholder::where('parent_id', $user_id)->get();
        // $shareholders->dd();
        return view('shareholder',[
            'shareholders' => $shareholders,
            'relationships' => $relationships,
        ]);
    }
    
    public function create(Request $request)
    {
        $request->validate([
            'first_name' => 'required|max:25|min:5',
            'last_name' => 'required|max:25',
            'date_of_birth' => 'nullable|date',
            'email' => 'required|email',
            'gender' => 'nullable|in:male,female,other',
            'relation' => 'required',
        ]);
        
        Shareholder::createShareholder($request);
        
    }
}
