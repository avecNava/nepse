<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MyShare;
use App\Models\Shareholder;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function importForm()
    {
        $user_id = Auth::id();

        //if no shareholder-id was supplied, choose parent account as default
        if(empty($shareholder_id)){
            $shareholder_id = Shareholder::where('parent_id', $user_id)->pluck('id')->first();
        }

        //get all the shareholder names to display in the select input
        $shareholders = Shareholder::where('parent_id', $user_id)->get();

        //get transaction history and its related stock_id, security_name from related (stocks table)
        $transactions = MyShare::where('shareholder_id', $shareholder_id)
                        ->with(['share'])
                        ->get();

        return view('import-share', [
                'transactions' => $transactions,
                'shareholders' => $shareholders->sortBy('first_name'),
                'shareholder_id' => $shareholder_id,
        ]);
    }

    public function guideline()
    {
        return view('guidelines');
    }
}
