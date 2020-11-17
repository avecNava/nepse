<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeroShare extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = "meroshare_transactions";

    public static function importTransactions($transactions)
    {
        $transactions->whenNotEmpty(function( $transactions ){
            foreach ($transactions as $trans ) {
                MeroShare::updateOrCreate(
                    [
                        'symbol' => $trans['symbol'],
                        'shareholder_id' => $trans['shareholder_id'],
                        'transaction_date' => $trans['transaction_date'],
                        'remarks' => $trans['remarks']
                    ],
                    [
                        'offer_type' => $trans['offer_type'],
                        'transaction_mode' => $trans['transaction_mode'],
                        'credit_quantity' => $trans['credit_quantity'],
                        'debit_quantity' => $trans['debit_quantity'],
                    ]);
            }
        });
    }

}
