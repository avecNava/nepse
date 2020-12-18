<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;
    protected $fillable = ['symbol','security_name'];

    public function news()
    {
        return $this->hasMany('App\Models\StockNews');
    }

    public function sector()
    {
        //todo: get stock category 
    }

    /**
     * creates or updates stocks from the given array
     * input: array with symbol and security_name
     */
    public static function addOrUpdateStock(Array $stocks)
    {
        if( empty($stocks) ) return false;
        
        foreach ($stocks as $data) {
            
            Stock::updateOrCreate(
                [
                    'symbol' => $data['symbol'],
                    'security_name' => $data['securityName'] 
                ]
            );
                
        }
    }
}
