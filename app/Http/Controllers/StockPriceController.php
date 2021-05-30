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
        if(!UtilityService::tradingDay($time_start)){
            return response()->json([
                'message' => 'NEPSE is closed during Saturdays and Fridays',
                'date' => $time_start->toDayDateTimeString(),
            ]);
        }

        // $date_string =  "$time_start->year-$time_start->month-$time_start->day";
        $date_string =  $time_start->toDateString();
        // $date_string =  '2020-12-31';
        /* add trailing slash (/) at the end of base_uri to avoid empty response message*/
        $client = new Client([
            // 'base_uri' => 'https://newweb.nepalstock.com/api/nots/nepse-data/'
            'base_uri' => 'https://newweb.nepalstock.com.np/api/nots/nepse-data/'
        ]);

        $response = $client->request('GET',"today-price", [
            'query' => [
                'size' => '400',
                'businessDate' => $date_string
            ],
            'http_errors' => false,              //parse the response, not matter it's ok or error
            // 'verify' => false,
            'headers' => [
                'User-Agent' => uniqid()      //custom user-agent
            ]
        ]);
        
        try {            
            
                $body = $response->getBody();
                $content = $body->getContents();

                if(Str::of(Str::lower($content))->exactly('searched date is not valid.')){
                    $msg = "Scraping failed. Data unavailable.',['. $date_string";
                    Log::warning('Scraping failed. Data unavailable.',['date'=>$date_string,'message'=>$msg]);
                    return response()->json( ['message'=> $msg] );
                }
                
                $data_array = json_decode($content, true);
            
                Stock::addOrUpdateStock($data_array['content']);
                StockPrice::updateOrCreateStockPrice( $data_array['content'] );        
                StockPrice::updateStockIDs();
                $time_finish = Carbon::now();
                $time_elapsed = $time_start->diffInSeconds($time_finish);

                $arr_output = [
                    'time_taken' => $time_elapsed ."s",
                    '# records' => count($data_array['content']),
                    'start_time' => "$time_start",
                    'end_time' => "$time_finish",
                ];

                Log::info('Scraping nepalstock.', $arr_output);                
                return response()->json($arr_output);
                
            } catch (\Throwable $th) {
                Log::warning($th->getMessage());
                return response()->json( ['message'=> $th->getMessage()] );
            }
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
