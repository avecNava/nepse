<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockPrice extends Model
{
    use HasFactory;

    protected $guarded = [];        //all fields are mass assignable

    public static function updateOrCreateStockPrice(Array $stocks)
    {
        if(empty($stocks)) return;

        foreach ($stocks as $stock) {

            //https://laravel.com/docs/8.x/eloquent#updateorcreate
            StockPrice::updateOrCreate(
                [
                    'symbol' => $stock['symbol'],
                    'transaction_date' => $stock['businessDate']
                ],
                [
                    'open_price' => $stock['openPrice'],
                    'high_price' => $stock['highPrice'],
                    'low_price' => $stock['lowPrice'],
                    'previous_day_close_price' => $stock['previousDayClosePrice'],
                    
                    'total_traded_qty' => $stock['totalTradedQuantity'],
                    'total_traded_value' => $stock['totalTradedValue'],
                    'total_trades' => $stock['totalTrades'],
                    
                    'avg_traded_price' => $stock['averageTradedPrice'],
                    'fifty_two_week_high_price' => $stock['fiftyTwoWeekHigh'],
                    'fifty_two_week_low_price' => $stock['fiftyTwoWeekLow'],
                    
                    'last_updated_price' => $stock['lastUpdatedPrice'],
                    
                    'close_price' => empty($stock['previousDayClosePrice']) ? null : $stock['previousDayClosePrice'],
                    'last_updated_time' => empty($stock['lastUpdatedTime']) ? null : $stock['lastUpdatedTime']
                ]
            );
        }
    }

}
