<?php

namespace App\Http\Controllers;

use App\Models\MeroShare;
use App\Models\Portfolio;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    
    public function index()
    {
        $user_id = 5;
        $symbol="API";
        $portfolios = Portfolio::find($symbol)->shares()->get();
        // $portfolios = Portfolio::find($user_id)->shareholder()->select('first_name','last_name')->get();
        $portfolios->dd();
        return view("portfolio", ['portfolios' => $portfolios]);
    }
    
    public function portfolioDetails($symbol)
    {
        return response()->json(['script'=>$symbol]);
    }
    
    public function portfolio()
    {   
        $user_id = 5;
        $total_dr = 0;
        $total_cr = 0;
        $collection = collect([]);

        // $transactions = MeroShare::join('stocks', 'stocks.symbol', '=', 'meroshare_transactions.symbol')
        //     ->get(['meroshare_transactions.*','stocks.id']);

        //https://laravel.com/docs/8.x/eloquent-relationships#constraining-eager-loads
        // $users = App\Models\User::with(['posts' => function ($query) {
        //     $query->where('title', 'like', '%first%');
        // }])->get();
        
        $transactions = MeroShare::where('shareholder_id', $user_id)->with('share:id,symbol,security_name')->get();
        
        // var_dump($transactions->toArray()); dd();
        
        $temp = $transactions->groupBy('symbol');
        $temp->map(function($item) use($collection, $total_cr, $total_dr){
            
            foreach ($item as $value) {
                
                // dd($value);
                // dd($value->share->security_name);
                $total_cr += empty($value->credit_quantity) ? 0 : $value->credit_quantity;
                $total_dr += empty($value->debit_quantity) ? 0 : $value->debit_quantity;
                
                $portfolio = array(
                    'id' => $value->id,
                    'symbol' => $value->symbol,
                    'stock_id' => $value->id,
                    'quantity' => $total_cr - $total_dr,
                    'user_id' => $value->shareholder_id,
                    'shareholder_id' => $value->shareholder_id,
                    'security_name' => empty($value->share) ? null :  $value->share->security_name,
                    'stock_id' =>  empty($value->share) ? null : $value->share->id,
                );

                $collection->push( $portfolio );
                
            };
        });

        $collection->dd();

        foreach ($collection as $row) {
            Portfolio::updateOrCreate(
                [
                    'stock_id' => $row['stock_id'], 
                    'shareholder_id' => $row['shareholder_id']
                ],
                [
                    'symbol' => $row['symbol'], 
                    'quantity' => $row['quantity'], 
                    'user_id' => $row['shareholder_id'],
                ]
            );
        }
    }

    public function storeToPortfolio(Request $request)
    {
        $total_dr = 0;
        $total_cr = 0;
        $collection = collect([]);

        if( !empty($request->trans_id) ){

           $ids = Str::of($request->trans_id)->explode(',');
           $transactions = MeroShare::where(function($query) use($ids){
                               $query->whereIn('id', $ids);
                            })->get();

            $temp = $transactions->groupBy('symbol');
            $temp->map(function($item) use($collection, $total_cr, $total_dr){

                foreach ($item as $key => $value) {
                    $total_cr += empty($value->credit_quantity) ? 0 : $value->credit_quantity;
                    $total_dr += empty($value->debit_quantity) ? 0 : $value->debit_quantity;
                };

                $collection->push(
                    array(
                        'symbol' => $value->symbol,
                        'stock_id' => $value->id,
                        'quantity' => $total_cr - $total_dr,
                        'shareholder_id' => $value->shareholder_id,
                    )
                );
            });

            foreach ($collection as $row) {
                Portfolio::updateOrCreate(
                    [
                        'stock_id' => $row['stock_id'],
                        'shareholder_id' => $row['shareholder_id']
                    ],
                    [
                        'symbol' => $row['symbol'], 
                        'quantity' => $row['quantity'], 
                        'user_id' => $row['shareholder_id'],
                    ]
                );
            }      
            return response()->json([
                'message' => 'success',
                'transaction_id' => $collection,
           ]);
 
          }
          return response()->json([
               'message' => 'No data received.<br/>Please select the transactions that you would like to import.'
          ]);

    }   
}
