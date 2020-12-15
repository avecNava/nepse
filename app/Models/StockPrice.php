<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class StockPrice extends Model
{
    use HasFactory;

    protected $guarded = [];        //all fields are mass assignable

    public function share()
    {
        return $this->belongsTo(Stock::class,'symbol','symbol');
    }

    //todo : use transaction
    public static function updateOrCreateStockPrice(Array $stock_prices)
    {
        if(empty($stock_prices)) return;

        DB::transaction(function() use($stock_prices){
            
            foreach ($stock_prices as $record) {

                StockPrice::updateOrCreate(
                    [
                        'symbol' => $record['symbol'],
                        'transaction_date' => $record['businessDate']
                    ],
                    [
                        'open_price' => $record['openPrice'],
                        'high_price' => $record['highPrice'],
                        'low_price' => $record['lowPrice'],
                        'close_price' => empty($record['closePrice']) ? null : $record['closePrice'],
                        'previous_day_close_price' => $record['previousDayClosePrice'],
                        'last_updated_price' => $record['lastUpdatedPrice'],
                        
                        'total_traded_qty' => $record['totalTradedQuantity'],
                        'total_traded_value' => $record['totalTradedValue'],
                        'total_trades' => $record['totalTrades'],
                        
                        'avg_traded_price' => $record['averageTradedPrice'],
                        'fifty_two_week_high_price' => $record['fiftyTwoWeekHigh'],
                        'fifty_two_week_low_price' => $record['fiftyTwoWeekLow'],
                        'last_updated_time' => empty($record['lastUpdatedTime']) ? null : $record['lastUpdatedTime']
                    ]
                );
            }

        });

    }


    /**
     * get stocks with null stock-id and update them
     */
    public static function updateStockIDs()
    {
        
        $transactions =  StockPrice::select('id','symbol')->where('stock_id', null)
                            ->with('share')->get();

            foreach ($transactions as $record) {
                $stock = StockPrice::find($record->id);
                $stock->stock_id = !empty($record->share->id) ? $record->share->id : null; 
                $stock->save();
            }

    }

    /**
     * gets the last transaction date from stock_prices table
     */
    public static function getLastDate()
    {
        $date = StockPrice::select('transaction_date')->orderBy('transaction_date','desc')->first();
        
        if(empty($date))
            return null;

        return $date->transaction_date;
    }
    
    /**
     * gets the last transaction price from stock_prices table
     */
    public static function getPrice(string $symbol)
    {
        
        if($symbol){

            $record = StockPrice::where('symbol',$symbol)
            ->with(['share'])
            ->orderBy('transaction_date','desc')->first();
            
            return $record;
        }

    }

}