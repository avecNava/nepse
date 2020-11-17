<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MeroShare;
use App\Models\Portfolio;

class PortfolioController extends Controller
{
    
    public function portfolio()
    {
        $total_dr = 0;
        $total_cr = 0;
        $collection = collect([]);
        $transactions = MeroShare::all();
        // where(function($query){
        //     $query->where('symbol','CBBL')
        //           ->orWhere('symbol','API');
        // })->orderBy('symbol')->get();

        $temp = $transactions->groupBy('symbol');
        $temp->map(function($item) use($collection, $total_cr, $total_dr){

            foreach ($item as $key => $value) {
                $total_cr += empty($value->credit_quantity) ? 0 : $value->credit_quantity;
                $total_dr += empty($value->debit_quantity) ? 0 : $value->debit_quantity;
            };

            $collection->push(
                array(
                    'symbol' => $value->symbol,
                    'quantity' => $total_cr - $total_dr,
                    'user_id' => $value->shareholder_id,
                    'shareholder_id' => $value->shareholder_id,
                )
            );
        });

        foreach ($collection as $row) {
            Portfolio::updateOrCreate(
                [
                    'symbol' => $row['symbol'], 
                    'shareholder_id' => $row['shareholder_id']
                ],
                [
                    'quantity' => $row['quantity'], 
                    'user_id' => $row['shareholder_id'],
                ]
            );
        }
    }

    public function storeToPortfolio(Request $request)
    {
          if( !empty($request)){

            //    $shareholder_id = $request->shareholder_id;
            //    $ids = Str::of($request->trans_id)->explode(',');
            //    $transactions = MeroShare::where(function($query) use($ids, $shareholder_id){
            //                        $query->whereIn('id', $ids)
            //                   })->where(function($query){
            //                        $query->where('shareholder_id', $shareholder_id)
            //                   })->get();
                
               
                // $transactions->groupBy();
                
                // DB::transaction(function(){

                // foreach ($transactions as $row) {
                //     Portfolio::updateOrCreate([
                //         ['symbol' => $row->symbol, 'shareholder_id' => $row->shareholder_id],
                //         [
                //             'quantity' => $row->credit_quantity, 
                //             'purchase_date' => $row->transaction_date,
                //             'offer_type' => $row->offer_type,
                //             'created_by' => 1,
                //             'user_id' => 1,
                //         ]
                //     ]);
                // }

                // });        
          
          }

          //{"message":"success","transaction_id":"1,2,3,4","shareholder_id":"2"}
          return response()->json([
               'message' => 'success',
               'transaction_id' => $transactions,
               'shareholder_id' => $request->shareholder_id,
          ]);

    }   
}
