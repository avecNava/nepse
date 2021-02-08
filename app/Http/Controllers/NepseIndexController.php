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
        $baseURL = 'https://newweb.nepalstock.com/api/nots/';
        $this->client = new client([
            'base_uri' => 'https://newweb.nepalstock.com/api/nots/index'
        ]);
    }
    
    /***
     * reads index history via https://newweb.nepalstock.com/api/nots/index/history/58 and stores in db
     * Runs : once a day after nepse market closes (except holidays and weekends)
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
     * reads index history via https://newweb.nepalstock.com/api/nots/graph/index/58 and stores in db
     * Runs : once every 15 minutes every day except holidays and weekends
     */
    public function currentIndex()
    {
    
        if(!UtilityService::tradingDay($this->businessDate)){
            return response()->json([
                'message' => 'NEPSE is closed during Saturdays and Fridays',
            ]);
        }

        //58 is for NEPSE Index
        $response = $this->client->request('GET',"graph/index/58", [
            'http_errors' => false              //parse the response, not matter it's ok or error
        ]);

        try {
        
            $body = $response->getBody();
            $content = $body->getContents();
            $data_array = json_decode($content, true);
            
            $businessDate = $this->epochToDate($data_array[0][0]);
            $all_indexes = collect([]);
            foreach($data_array as $data){ 
                $epoch = $data[0];
                $index = $data[1];
                
                $all_indexes->push([
                    'epoch' => $epoch,
                    'index'=> $index,
                    'transactionDate' => $this->epochToDate($epoch),
                ]);
            }
            
            //https://laravel.com/docs/8.x/eloquent#upserts
            DailyIndex::upsert(
                $all_indexes->toArray(),            //all values
                ['epoch'],                          //value to check for duplicates
                ['index','transactionDate']                           //update this value if duplicate
            );

            DailyIndex::where('transactionDate','<', $businessDate)->delete();
            NepseIndex::updateCurrentIndex();
            
            return response()->json("Current index recorded");                
            } catch (\Throwable $th) {
                Log::warning('NEPSE Current index - ' . $th->getMessage());
                return response()->json( ['message'=> $th->getMessage()] );
            }
    }

    public function epochToDate($epoch)
    {   
            $index_date = new \DateTime("@$epoch");
            $time_string = $index_date->format('Y-m-d H:i:s');
            $display_time = new \DateTime($time_string, new \DateTimeZone('UTC'));
            $display_time->setTimeZone(new \DateTimeZone('Etc/GMT-6'));             //Ideally the timezone should be Asia/Kathmandu
            // return $display_time->format('Y-m-d H:i:s');
            return $display_time->format('Y-m-d');
    }

}
