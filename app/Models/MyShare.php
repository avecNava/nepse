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

         try {
             
            MyShare::create([
                'symbol' => empty($item['symbol']) ? null : $item['symbol'],
                'purchase_date' => empty($date_str) ? null : $date_str,
                'description' => empty($item['description']) ? null : $item['description'],
                'offer_code' => empty($item['offer_code']) ? null : $item['offer_code'],
                'quantity' => empty($item['quantity']) ? null : $item['quantity'],
                'unit_cost' => empty($item['unit_cost']) ? null : $item['unit_cost'],
                'shareholder_id' => (int)$item['shareholder_id'],
                'effective_rate' => empty($item['effective_rate']) ? null : $item['effective_rate'],
            ]);

        } catch (\Throwable $th) {
            return false;
        }
        
        return true;

        });

    }
    
}
