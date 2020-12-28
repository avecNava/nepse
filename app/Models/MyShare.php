<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MyShare extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function share()
    {
        //related model, foreign_key in current model (meroshare_transactions), related column in the related model
        return $this->belongsTo('App\Models\Stock','symbol','symbol');
    }

    public function shareholder()
    {
        //map shareholder_id of current model to the id field of shareholders table (shareholder_id)
        return $this->belongsTo('App\Models\Shareholder','shareholder_id');
    }

    public function offer()
    {
        return $this->belongsTo(StockOffering::class, 'offer_code', 'offer_code');
    }

    public static function importTransactions($transactions)
    {
        
        $transactions->each(function($item){

            $date = Carbon::now();
            $date_str = $date->format('Y-m-d');
   
            try {
                $date = $item['purchase_date'];
                $date_str = $date->format('Y-m-d');
            } catch (\Throwable $th) {
                //throw $th;
            }
         
            MyShare::create([
                'symbol' => $item['symbol'],
                'purchase_date' => $date_str,
                'description' => $item['description'],
                'offer_code' => $item['offer_code'],
                'quantity' => $item['quantity'],
                'unit_cost' => $item['unit_cost'],
                'shareholder_id' => (int)$item['shareholder_id'],
                'effective_rate' => $item['effective_rate'],
            ]);
    

        });

    }
    
}
