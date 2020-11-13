<?php

namespace App\Http\Controllers;

use App\Models\StockPrice;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Illuminate\Support\Str;


class StockPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $date_string = '2020-11-13';

        $client = new client([
            'base_uri' => 'https://newweb.nepalstock.com/api/nots/nepse-data/'
        ]);

        $response = $client->request('GET',"today-price", [
            'query' => [
                'size' => '500',                            
                'businessDate' => $date_string
            ],
            'http_errors' => false              //parse the response, not matter it's ok or error
            ]);
            
        $body = $response->getBody();
        $content = $body->getContents();

        if(Str::of(Str::lower($content))->exactly('searched date is not valid.')){
            return 'Searched Date is not valid';
        }

        $data_array = json_decode($content, true);
        
        foreach ($data_array['content'] as $stock) {
            
            \DB::table('stocks')->insert([
                'symbol' => $stock['symbol'],
                'security_name' => $stock['securityName'],
                'user_id' => 1,
                'created_at' => Carbon::now()->toDateTimeString()
                ]);
                
            \DB::table('stock_prices')->insert([
                'symbol' => $stock['symbol'],
                'security_name' => $stock['securityName'],   
                // 'category_id' =>    
                
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
                
                // 'total_sell_qty' => 0,
                // 'total_buy_qty' => 0,
                
                'last_updated_price' => $stock['lastUpdatedPrice'],
                
                'close_price' => empty($stock['previousDayClosePrice']) ? null : $stock['previousDayClosePrice'],
                'last_updated_time' => empty($stock['lastUpdatedTime']) ? null : $stock['lastUpdatedTime'],
                'transaction_date' => $stock['businessDate'],
                'created_at' => Carbon::now()->toDateTimeString()
                ]);
            }
        return $data_array;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\StockPrice  $stockPrice
     * @return \Illuminate\Http\Response
     */
    public function show(StockPrice $stockPrice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StockPrice  $stockPrice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StockPrice $stockPrice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StockPrice  $stockPrice
     * @return \Illuminate\Http\Response
     */
    public function destroy(StockPrice $stockPrice)
    {
        //
    }
}
