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
use Goutte\Client as GoutteClient;
use Symfony\Component\HttpClient\HttpClient;


class StockPriceController extends Controller
{
    
    public function latestPrice($date=null, $id=0)
    {

        $time_start = Carbon::now();
        
        if($date){
            $arr = explode('-',$date);
            $year = $arr[0]; $month = $arr[1]; $day = $arr[2];
            $hour = 11; $minute = 0; $second = 0; $tz = 'Asia/Kathmandu';
            $time_start = Carbon::parse(Carbon::createFromDate($year, $month, $day, $tz));
        }
        
        $date_string =  $time_start->toDateString();

        //do not proceed for non working days
        if(!UtilityService::tradingDay($time_start)){
            return response()->json([
                'message' => 'NEPSE is closed during Saturdays and Fridays',
                'date' => $time_start->toDayDateTimeString(),
            ]);
        }

        
        //URL resolution (PSR) : https://datatracker.ietf.org/doc/html/rfc3986#section-5.2
        // $url = 'https://newweb.nepalstock.com/api/nots/nepse-data/today-price?';
        $base_uri = 'https://newweb.nepalstock.com.np';
        
        $client = new Client([
            'base_uri' => $base_uri,
            // 'debug' => true,
            // 'body' => json_encode(['id'=>'330']),
            'json'=> [
                'id' => $id == 0 ? rand(100,999) : $id,     //id validates the request
            ],
            'query' => [
                'size' => '400',
                'businessDate' => $date_string,
            ],
            'headers' => [
                'User-Agent'=> 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0',
                'content-type'=>'application/json',
                'accept' => '*/*',
                'content-encoding'=>'gzip',
                'Accept-Encoding'=> 'gzip, deflate, br',
                // 'Content-Length'=> '10',
                'Origin'=> 'https://newweb.nepalstock.com',
                'Referer'=> 'https://newweb.nepalstock.com/today-price',
            ],
        ]);

        $response = $client->request('POST',"/api/nots/nepse-data/today-price?",  
        [
            'http_errors' => FALSE,
        ]);

        // $code = $response->getStatusCode(); // 200, 400
        // $reason = $response->getReasonPhrase(); // OK, Bad Request
        
        try {            
            
            $content = $response->getBody()->getContents();
          
            if(Str::of(Str::lower($content))->exactly('searched date is not valid.')){
                $msg = "Scraping failed. Data not available for $date_string";
                Log::warning('Scraping FAILED',['date'=>$date_string,'method'=>'today-price','message'=>$msg]);
                return response()->json( ['message'=> $msg] );
            }
            
            $data = json_decode($content, true)['content'];
            
            $url = "$base_uri/today-price?size=400&businessDate=$date_string";
            if(sizeof($data) < 1){
                Log::error("Refreshing stock prices from $url. EMPTY RESPONSE");
                return "EMPTY RESPONSE<hr>$url";
            }

            Stock::addOrUpdateStock($data);
            StockPrice::updateOrCreateStockPrice($data);        
            StockPrice::updateStockIDs();

            $time_finish = Carbon::now();
            $time_elapsed = $time_start->diffInSeconds($time_finish);

            $arr_output = [
                'time_taken' => $time_elapsed ."s",
                '# records' => count($data),
                'start_time' => "$time_start",
                'end_time' => "$time_finish",
            ];

            Log::info("Refreshing stock prices from $url. DONE.", $arr_output);             
            return response()->json($arr_output);
            
        } catch (\Throwable $th) {
            // dd($th);
            Log::warning($th->getMessage());
            return response()->json( ['message'=> $th->getMessage()] );
        }
    }

    public function stockLive()
    {
        $time_start = Carbon::now();

        //stop request during non working days
        if(!UtilityService::tradingDay($time_start)){
            return response()->json([
                'message' => 'NEPSE is closed during Saturdays and Fridays',
                'date' => $time_start->toDayDateTimeString(),
            ]);
        }

        $base_uri = 'https://newweb.nepalstock.com.np/api/nots/';
        $client = new Client(['base_uri' => $base_uri]);

        $response = $client->request('GET',"lives-market", [
            'query' => [
                'size' => '400',
            ],
            'headers' => [
                'User-Agent' => 'Jay Nepal',
                'Accept'     => 'application/json',
            ],
        ]);

        try {            
            
            $body = $response->getBody();
            $content = $body->getContents();

            if(Str::of(Str::lower($content))->exactly('searched date is not valid.') || strlen($content)==0){
                $msg = "SYNC stocklive FAILED. Data not available.";
                Log::error('STOCKLIVE',['method'=>'stocklive','message'=>$msg]);
                return response()->json( ['message'=> $msg] );
            }
            
            $data_array = json_decode($content, true);

            $empty_msg  = "EMPTY RESPONSE received from $base_uri/lives-market";
            if(sizeof($data_array) < 1){
                Log::error($empty_msg);
                return $empty_msg;
            }

            // Stock::addOrUpdateStock($data);
            StockPrice::updateOrCreateStockPriceForStockLive( $data_array );        
            StockPrice::updateStockIDs();

            $time_finish = Carbon::now();
            $time_elapsed = $time_start->diffInSeconds($time_finish);

            $arr_output = [
                'time_taken' => $time_elapsed .'s',
                '# records' => count($data_array),
            ];

            Log::info('SYNC stocklive', $arr_output);             
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