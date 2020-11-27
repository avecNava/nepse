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
            'parent_id' => $user_id,
        ]);
    }
    
    /*** AJAX GET request for Shareholder
     *  input Shareholder_id
     *  returns JSON 
     * */
    public function getShareholder(Request $request, $id=null)
    {
        if(empty($id)){
            $id = $request->id;
        }
        
        $shareholder = Shareholder::where('id', $id)->first();
        return response()->json(['data'=>$shareholder]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'first_name' => 'required|max:25|min:3',
            'last_name' => 'required|max:25|min:3',
            'date_of_birth' => 'nullable|date',
            'email' => 'required|email',
            'gender' => 'nullable|in:male,female,other',
            'relation' => 'required',
        ]);
        
        Shareholder::createShareholder($request);
        
        return redirect()->back()->with('message', 'Record created or updated succesfully.');

    }
    public function delete(Request $request, $id=null)
    {
        if(empty($id)){
            $id = $request->id;             //get id from POST request
        }

        $deleted = Shareholder::destroy($id);
        return response()->json(['message'=>'deleted', 'count'=>$deleted]);
    }
}
