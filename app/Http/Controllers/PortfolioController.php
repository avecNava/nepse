<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    public function storeToPortfolio(Request $request)
    {
          if( !empty($request)){

               $shareholder_id = $request->shareholder_id;
               $ids = Str::of($request->trans_id)->explode(',');
               $transactions = MeroShare::where(function($query) use($ids, $shareholder_id){
                                   $query->whereIn('id', $ids)
                              })->where(function($query){
                                   $query->where('shareholder_id', $shareholder_id)
                              })->get();
                
                //TODO: group by symbol and calculate net quantity

                DB::transaction(function(){
                foreach ($transactions as $row) {
                    Portfolio::updateOrCreate([
                        ['symbol' => $row->symbol, 'shareholder_id' => $row->shareholder_id],
                        [
                            'quantity' => $row->credit_quantity, 
                            'purchase_date' => $row->transaction_date,
                            'offer_type' => $row->offer_type,
                            'created_by' => 1,
                            'user_id' => 1,
                        ]
                    ]);
                }
                });        
          
          }

          //{"message":"success","transaction_id":"1,2,3,4","shareholder_id":"2"}
          return response()->json([
               'message' => 'success',
               'transaction_id' => $transactions,
               'shareholder_id' => $request->shareholder_id,
          ]);

    }   
}
