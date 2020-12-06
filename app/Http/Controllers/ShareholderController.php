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
        $this->middleware('auth');
    }
    
    public function index()
    {
        $user_id = Auth::id();
        $relationships = Relation::orderBy('relation','asc')->get();

        //get all the shareholder names with the logged in account (ie, the parent)
        $shareholders = Shareholder::where('parent_id', $user_id)->get();

        return view('shareholder',[
            'shareholders' => $shareholders,
            'relationships' => $relationships,
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
        // dd($shareholder->toJson(JSON_PRETTY_PRINT));
        return $shareholder->toJson();
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
        
        //todo: check if the email address is unique (per parent shareholder)
        Shareholder::createShareholder($request);
        
        return redirect()->back()->with('message', 'Record created or updated succesfully.');

    }

    /**
     * delete the Shareholder
     * DONOT DELETE THE primary shareholder
     * DONOT DELETE shareholder if protfolio exists
     * $id is supplied via POST via AJAX request
     */

    public function delete(Request $request, $id)
    {
        $flag = false;

        $message = 'Shareholder id can not be null';
        if(empty($id)){
            $id = $request->id;             //get id from POST request
        }
        
        $shareholder = Shareholder::where('id', $id)->select('first_name','parent')->first();
        $member = Shareholder::where('id', 1)->withCount('portfolio')->first();

        if($shareholder->parent==true){
            $message = 'Can not delete a parent Shareholder';
        }
        //do not delete shareholders if they have protfolio
        elseif($member->portfolio_count > 0){
            $message = "😒 Can not delete ! Selected member has '$member->portfolio_count stocks' in record.";
        }
        else {
            $deleted = Shareholder::destroy($id);
            // $deleted = 1;
            if($deleted > 0){
                $message = "Shareholder $shareholder->first_name deleted.";
                $flag = true;
            }
        }

        return response()->json(['action'=>'delete', 'message'=> $message, 'status'=>$flag]);
    }

}
