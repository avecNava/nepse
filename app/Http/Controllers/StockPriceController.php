<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\StockPrice;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
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
        $date_string = '2020-11-12';

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
        
        \App\Models\Stock::addOrUpdateStock($data_array['content']);
        \App\Models\StockPrice::updateOrCreateStockPrice($data_array['content']);

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
