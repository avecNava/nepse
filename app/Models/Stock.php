<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\StockPrice;
use App\Models\StockNews;
use App\Models\StockSector;
use App\Models\User;

class Stock extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function news()
    {
        return $this->hasMany(StockNews::class);
    }

    public function prices()
    {
        return $this->hasMany(StockPrice::class);
    }
    
    public function sector()
    {
        return $this->belongsTo(StockSector::class, 'sector_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'last_modified_by');
    }

    public static function getSymbol($id)
    {
        $temp = Stock::find($id);
        return optional($temp)->symbol;
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
                'symbol' => Str::of($data['symbol'])->trim(),
            ],
            [
                'security_name' => Str::of($data['securityName'])->trim(),                    
            ]);
                
        }
    }

    public static function getStockDetail($symbol)
    {
        return Stock::where('symbol', $symbol)->with('sector:sector,id')->first();
    }
}
