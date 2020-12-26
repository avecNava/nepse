<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyShare extends Model
{
    use HasFactory;

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
        $transactions->whenNotEmpty(function() use($transactions){
            
            foreach ($transactions as $trans ) {
                MyShare::create([
                    'symbol' => $trans['symbol'],
                    'purchase_date' => $trans['purchase_date'],
                    'description' => $trans['description'],
                    'offer_code' => $trans['offer_type'],
                    'quantity' => $trans['quantity'],
                    'unit_cost' => $trans['unit_cost'],
                    'shareholder_id' => $trans['shareholder_id'],
                    'effective_rate' => $trans['effective_rate'],
                ]);
            }

        });
    }
    
}
