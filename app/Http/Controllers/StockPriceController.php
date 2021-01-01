<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\StockPrice;
use App\Services\UtilityService;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class StockPriceController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //todo: check holidays - check saturdays and fridays

        $time_start = Carbon::now();
        
        // $time_start = $time_start->sub('3 days');
     
        //stop request during non working days
        // if(!UtilityService::tradingDay($time_start)){
        //     return response()->json([
        //         'message' => 'NEPSE is closed during Saturdays and Fridays',
        //         'date' => $time_start->toDayDateTimeString(),
        //     ]);
        // }

        // $date_string =  "$time_start->year-$time_start->month-$time_start->day";
        $date_string =  $time_start->toDateString();
        $date_string =  '2020-12-31';
        
        $client = new client([
            'base_uri' => 'https://newweb.nepalstock.com/api/nots/nepse-data/'
        ]);

        $response = $client->request('GET',"today-price", [
            'query' => [
                'size' => '600',                            
                'businessDate' => $date_string
            ],
            'http_errors' => false              //parse the response, not matter it's ok or error
            ]);
            
        $body = $response->getBody();
        $content = $body->getContents();

        if(Str::of(Str::lower($content))->exactly('searched date is not valid.')){
            $msg = "Scraping failed. No data available. $date_string";
            Log::warning('Scraping failed. No data available.',['date'=>$date_string,'message'=>$msg]);
            return response()->json(['message'=>$msg]);
        }
        
        $data_array = json_decode($content, true);
    
        // $data = array(["id" => 1262446,"businessDate" => "2020-11-19",
        //         "securityId" => 2893,"symbol" => "AIL",
        //         "securityName" => "Ajod Insurance Limited",
        //         "openPrice" => 530.0,"highPrice" => 574.0,
        //         "lowPrice" => 527.0,"closePrice" => 563.0,
        //         "totalTradedQuantity" => 75950,"totalTradedValue" => 41735965.0,
        //         "previousDayClosePrice" => 526.0,"fiftyTwoWeekHigh" => 580.0,
        //         "fiftyTwoWeekLow" => 291.0,"lastUpdatedTime" => "2020-11-19T14:59:59.711318",
        //         "lastUpdatedPrice" => 563.0,"totalTrades" => 1153,"averageTradedPrice" => 549.52]);
        // StockPrice::updateOrCreateStockPrice($data);        

        Log::notice('Started scraping from nepalstock',['date' => $date_string]);

        Stock::addOrUpdateStock($data_array['content']);
        StockPrice::updateOrCreateStockPrice( $data_array['content'] );        
        StockPrice::updateStockIDs();
        $time_finish = Carbon::now();
        $time_elapsed = $time_start->diffInSeconds($time_finish);

        Log::notice('Finished scraping from nepalstock',
            [
                'date'=>$date_string, 
                'time'=>$time_elapsed.' seconds'
            ]);
        
        echo "Time taken : $time_elapsed seconds";
        return $data_array['content'];
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
     * @param  StockPrice  $stockPrice
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
     * @param  StockPrice  $stockPrice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StockPrice $stockPrice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  StockPrice  $stockPrice
     * @return \Illuminate\Http\Response
     */
    public function destroy(StockPrice $stockPrice)
    {
        //
    }
}
