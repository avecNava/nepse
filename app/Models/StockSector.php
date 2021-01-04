<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Stock;
use App\Models\StockPrice;

class StockSector extends Model
{
    use HasFactory;

    public function stocks()
    {
        return $this->hasMany(Stock::class,'sector_id');            //fk on Stocks
    }

    public function price()
    {
        $transaction_date = StockPrice::getLastDate();
        return $this->hasManyThrough(
            StockPrice::class, 
            Stock::class,
            'sector_id',                //fk on [stocks] table
            'stock_id',                 //fk  on stockprices table
            'id',                       //key on sectors table
            'id'                        //key on [stocks] table
        )
        ->where('transaction_date', $transaction_date);
    }

}
