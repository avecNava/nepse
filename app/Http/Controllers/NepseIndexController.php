<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\UtilityService;
use App\Models\NepseIndex;
use App\Models\DailyIndex;

class NepseIndexController extends Controller
{
    private $businessDate;
    private $client;

    public function __construct(){
        
        $today = Carbon::now();
        $this->businessDate =  $today->toDateString();
        // $baseURL = 'https://newweb.nepalstock.com/api/nots/';
        // 62 Float
        // 63 Sen Float
        // 58 Index
        // 57 Sensitive Index
        $this->client = new client([
            'base_uri' => 'https://newweb.nepalstock.com.np/api/nots/'
        ]);
    }
    
    /***
     * Datewise indices 
     * https://newweb.nepalstock.com.np/api/nots/index/history/58?&size=5 (take only 5 days)
     * and stores in db
     * Runs : once a day after nepse market closes (except holidays and weekends)
     * Note : This will set the closing Index for current date 0 if run during business hours (as closing index is not yet calculated)
     */
    public function indexHistory()
    {
        
        if(!UtilityService::tradingDay($this->businessDate)){
            return response()->json([
                'message' => 'NEPSE is closed during Saturdays and Fridays',
                'date' => $this->businessDate,
            ]);
        }

        //58 is for NEPSE Index
        $response = $this->client->request('GET',"index/history/58", 
        [
            'query' => ['size' => '5'],
            'headers' => [
                'User-Agent' => uniqid()        //custom user-agent
            ],
            'http_errors' => false              //parse the response, not matter it's ok or error
        ]);
        

        try {
        
                $body = $response->getBody();
                $content = $body->getContents();
             
                $data_array = json_decode($content, true);
                
                foreach ($data_array['content'] as $data) {
                    NepseIndex::updateOrCreate(
                        [
                            'transactionDate'=>$data['businessDate'],
                        ],
                        [
                            'closingIndex'=>$data['closingIndex'],
                            'openIndex'=>$data['openIndex'],
                            'highIndex'=>$data['highIndex'],
                            'lowIndex'=>$data['lowIndex'],
                            'fiftyTwoWeekHigh'=>$data['fiftyTwoWeekHigh'],
                            'fiftyTwoWeekLow'=>$data['fiftyTwoWeekLow'],
                    ]);
                }
                
                Log::info('NEPSE Index history recorded.', [$this->businessDate]);
                return response()->json("Index scraped successfully - $this->businessDate");
                
            } catch (\Throwable $th) {
                Log::warning('NEPSE Index - ' . $th->getMessage());
                return response()->json( ['message'=> $th->getMessage()] );
            }

    }

    /***
     * Current index for various business hours throughout the day
     * (https://newweb.nepalstock.com.np/api/nots/graph/index/58)
     * https://newweb.nepalstock.com.np/api/nots/securityDailyTradeStat/58
     * Runs : once every 15 minutes every day except holidays and weekends
     * updates
     */
    public function currentIndex($id=0)
    {
    
        if(!UtilityService::tradingDay($this->businessDate)){
            return response()->json([
                'message' => 'NEPSE is closed during Saturdays and Fridays',
            ]);
        }
        
        //58 is for NEPSE Index
        $response = $this->client->request('POST',"graph/index/58", 
            [
                'json'=> [
                    'id' => ($id == 0) ? rand(100,999) : $id,     //id validates the request
                ],
                'headers' => [
                    'User-Agent'=> 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0',
                    'content-type'=>'application/json',
                    'accept' => '*/*',
                    'content-encoding'=>'gzip',
                    'Accept-Encoding'=> 'gzip, deflate, br',
                ],
        ]);
        
        try {
            
            $body = $response->getBody();
            $content = $body->getContents();
            $data_array = json_decode($content, true);
            //get businessDate from epoch (date since 1/1/1970)
            $businessDate = UtilityService::getDateFromEpoch($data_array[0][0]);
            $all_indexes = collect([]);
            
            foreach($data_array as $data){ 
                $epoch = $data[0];
                $index = $data[1];
                
                $all_indexes->push([
                    'epoch' => $epoch,
                    'index'=> $index,
                    'transactionDate' => UtilityService::getDateFromEpoch($epoch),
                ]); 
            }
            //https://laravel.com/docs/8.x/eloquent#upserts
            DailyIndex::upsert(
                $all_indexes->toArray(),            //all values
                ['epoch'],                          //value to check for duplicates
                ['index','transactionDate']         //update this value if duplicate
            );

            DailyIndex::where('transactionDate','<', $businessDate)->delete();
            //update NepseIndex table with the latest index
            NepseIndex::updateCurrentIndex();
            
            return response()->json("Current index recorded");                
            } catch (\Throwable $th) {
                Log::warning('NEPSE Current index - ' . $th->getMessage());
                return response()->json( ['message'=> $th->getMessage()] );
            }
    }

}
