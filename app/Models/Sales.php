<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Traits\BelongsToTenant;

class Sales extends Model
{
    use HasFactory, BelongsToTenant;
    protected $guarded = [];

    public function shareholder()
    {
        return $this->belongsTo('App\Models\Shareholder', 'shareholder_id');
    }

    public function share()
    {
        return $this->belongsTo('App\Models\Stock', 'stock_id');
    }
    
    public function price()
    {
        return $this->belongsTo('App\Models\StockPrice', 'stock_id','stock_id')->where('latest', true);
    }

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
                'quantity' => $row['quantity'], 
                'sales_date' => $row['transaction_date'],
            ],
            [
                'offer_id' => $row['offer_id'],
                'last_modified_by' => Auth::id(),
                'remarks' => $row['remarks'],
            ]);
        }
    }


}
