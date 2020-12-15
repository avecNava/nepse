<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeroShare extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = "meroshare_transactions";

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
    
    /***
     * saves transactions as portfolio
     */
    public static function importTransactions($transactions)
    {
        $transactions->whenNotEmpty(function() use($transactions){
            
            foreach ($transactions as $trans ) {
                MeroShare::create([
                    'symbol' => $trans['symbol'],
                    'shareholder_id' => $trans['shareholder_id'],
                    'transaction_date' => $trans['transaction_date'],
                    'remarks' => $trans['remarks'],
                    'offer_code' => $trans['offer_type'],
                    'transaction_mode' => $trans['transaction_mode'],
                    'credit_quantity' => $trans['credit_quantity'],
                    'debit_quantity' => $trans['debit_quantity'],
                ]);
                // MeroShare::updateOrCreate(
                // [
                //     'symbol' => $trans['symbol'],
                //     'shareholder_id' => $trans['shareholder_id'],
                //     'transaction_date' => $trans['transaction_date'],
                //     'remarks' => $trans['remarks']
                // ],
                // [
                //     'offer_type' => $trans['offer_type'],
                //     'transaction_mode' => $trans['transaction_mode'],
                //     'credit_quantity' => $trans['credit_quantity'],
                //     'debit_quantity' => $trans['debit_quantity'],
                // ]);
            }

        });
    }

}
