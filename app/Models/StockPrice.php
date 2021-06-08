<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;


class StockPrice extends Model
{
    use HasFactory;

    protected $guarded = [];        //all fields are mass assignable

    public function share()
    {
        return $this->belongsTo(Stock::class,'symbol','symbol');
    }

    /**
     * get sector name via stock table using hasOneThrough relatinship
     * https://laravel.com/docs/8.x/eloquent-relationships#has-one-through
     */
    public function sector()
    {
        return $this->hasOneThrough(
            \App\Models\StockSector::class,
            \App\Models\Stock::class,
            'id',
            'id',
            'stock_id',
            'sector_id',
        );
    }

    /**
     * addes the stock prices into the stock_prices table
     * a service scrapes nepse trade data every day and prices are saved to stock_prices table
     * input1: an array with symbol, transaction_date, close_price, open_price and other price details
     * input2: a date string indicating date for which transactions have been imported
     */
    public static function updateOrCreateStockPrice(Array $stock_prices)
    {
        if(empty($stock_prices)) return;
        
        $symbols = collect([]);
        $trade_date =  $stock_prices[0]['businessDate'];        

        //task-1 : save new price, set latest=true
        DB::transaction(function() use($stock_prices, $symbols){
            
            foreach ($stock_prices as $record) {

                $symbols->push($record['symbol']);

                StockPrice::updateOrCreate(
                    [
                        'symbol' => $record['symbol'],
                        'transaction_date' => $record['businessDate']
                    ],
                    [
                        'latest' => true,
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
                        'last_updated_time' => empty($record['lastUpdatedTime']) ? null : $record['lastUpdatedTime'],
                        'created_at' => Carbon::now(),
                    ]
                );
            }

        });

        //task-2 : query all records with given symbol, transaction_date (from input array) and latest=true
        //set latest to false

        StockPrice::whereIn('symbol', $symbols->toArray())
            ->where('transaction_date','<>', $trade_date)                 //or use != instead
            ->where('latest',true)
            ->update(['latest' => false]);

        //update `stock_prices` set `latest` = 0, `stock_prices`.`updated_at` = '2021-01-01 05:44:44' 
        //where `symbol` in ('NSEWA') and `transaction_date` <> '2020-12-31' and `latest` = 1
        
    }
   
    //similar as above function but modified for stocklive data only
    public static function updateOrCreateStockPriceForStockLive(Array $stock_prices)
    {
        if(empty($stock_prices)) return;
        
        $symbols = collect([]);
        $trade_date = substr($stock_prices[0]['lastUpdatedDateTime'],0,10);
        
        //task-1 : save new price, set latest=true
        DB::transaction(function() use($stock_prices, $symbols) {
            
            foreach ($stock_prices as $record) {

                $symbols->push($record['symbol']);

                StockPrice::updateOrCreate(
                    [
                        'symbol' => $record['symbol'],
                        'transaction_date' => substr($record['lastUpdatedDateTime'],0,10),
                    ],
                    [
                        'latest' => true,
                        'open_price' => $record['openPrice'],
                        'high_price' => $record['highPrice'],
                        'low_price' => $record['lowPrice'],
                        'close_price' => empty($record['lastTradedPrice']),
                        'last_updated_price' => $record['lastTradedPrice'],
                        'previous_day_close_price' => $record['previousClose'],
                        'total_traded_qty' => $record['totalTradeQuantity'],
                        'total_traded_value' => $record['totalTradeQuantity'] * $record['lastTradedPrice'],
                        
                        // 'total_trades' => $record['totalTrades'],
                        // 'avg_traded_price' => $record['averageTradedPrice'],
                        // 'fifty_two_week_high_price' => $record['fiftyTwoWeekHigh'],
                        // 'fifty_two_week_low_price' => $record['fiftyTwoWeekLow'],
                        'last_updated_time' => $record['lastUpdatedDateTime'] ,
                        'created_at' => Carbon::now(),
                    ]
                );
            }

        });

        //task-2 : query all records with given symbol, transaction_date (from input array) and latest=true
        //set latest to false

        StockPrice::whereIn('symbol', $symbols->toArray())
            ->where('transaction_date','<>', $trade_date)                 //or use != instead
            ->where('latest',true)
            ->update(['latest' => false]);

        //update `stock_prices` set `latest` = 0, `stock_prices`.`updated_at` = '2021-01-01 05:44:44' 
        //where `symbol` in ('NSEWA') and `transaction_date` <> '2020-12-31' and `latest` = 1
        
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
        return StockPrice::max('transaction_date');

    }
    
    /**
     * gets the last transaction date and time from stock_prices table
     */
    public static function getLastTransactionDate( $stock_id = null )
    {
        if($stock_id){
            return StockPrice::where('id', $stock_id)->max('last_updated_time');
        }
        return StockPrice::max('last_updated_time');

    }
    
    public function scopeLastTradePrice($query)
    {
        return $query->where('latest',true);
    }
    
    /**
     * gets the last transaction price from stock_prices table
     */
    public static function getPrice($stock_id)
    {
        
        if($stock_id){

            $record = StockPrice::where('stock_id', $stock_id)
            ->with(['share'])
            ->LastTradePrice()->first();
            
            return $record;
        }

    }

}