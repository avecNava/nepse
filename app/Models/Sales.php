<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Log;

class Sales extends Model
{
    use HasFactory;
    protected $guarded = [];

     /**
     * update or create portfolios based on input object
     */
    public static function updateOrCreateSales($records)
    {
        foreach ($records as $row) {
 
            //update record if the following five attributes are met,
            //else not create a new record with the following attributes

            Sales::updateOrCreate(
            [
                'stock_id' => $row['stock_id'], 
                'shareholder_id' => $row['shareholder_id'],
                'offer_id' => $row['offer_id'],
                'quantity' => $row['quantity'], 
                'sales_date' => $row['transaction_date'],
            ],
            [
                'last_modified_by' => Auth::id(),
                'remarks' => $row['remarks'],
            ]);
        }
    }


}
