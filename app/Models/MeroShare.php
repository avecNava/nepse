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
                MeroShare::create([
                    'symbol' => $trans['symbol'],
                    'shareholder_id' => $trans['shareholder_id'],
                    'credit_quantity' => $trans['credit_quantity'],
                    'debit_quantity' => $trans['debit_quantity'],
                    'offering_type' => $trans['offering_type'],
                    'transaction_mode' => $trans['transaction_mode'],
                    'remarks' => $trans['remarks'],
                ]);
            }
        });
    }

}
