<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\UtilityService;
use App\Models\NepseIndex;

class NepseIndexController extends Controller
{
    
    public function index()
    {
    
        $time_start = Carbon::now();
        if(!UtilityService::tradingDay($time_start)){
            return response()->json([
                'message' => 'NEPSE is closed during Saturdays and Fridays',
                'date' => $time_start->toDayDateTimeString(),
            ]);
        }
        $date_string =  $time_start->toDateString();
        
        $client = new client([
            'base_uri' => 'https://newweb.nepalstock.com/api/nots/index/'
        ]);

        //58 is for NEPSE Index
        $response = $client->request('GET',"history/58", [
            'query' => [
                'size' => '500',
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
                            'businessDate'=>$data['businessDate'],
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
                
                Log::info('NEPSE Index scraped successfully', [$date_string]);
                return response()->json("Index scraped successfully $date_string");
                
            } catch (\Throwable $th) {
                Log::warning('NEPSE Index - ' . $th->getMessage());
                return response()->json( ['message'=> $th->getMessage()] );
            }
    }
}
