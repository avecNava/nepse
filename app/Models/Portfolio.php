<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function shareholder()
    {
        return $this->belongsTo('App\Models\Shareholder','shareholder_id');
    }

    public function share()
    {
        return $this->belongsTo('App\Models\Stock','stock_id');
    }

    public function stockPrice()
    {
        return $this->belongsTo('App\Models\StockPrice','stock_id','stock_id');
    }

    public function lastPrice()
    {
        $transaction_date = StockPrice::getLastDate();
        return $this->belongsTo('App\Models\StockPrice','stock_id','stock_id')->where('transaction_date','=',$transaction_date);
    }   

    /**
     * get sector name via stocks table -> hasOneThrough
     * //https://laravel.com/docs/8.x/eloquent-relationships#has-one-through
     * 
     *  first argument : name of final model we wish to access (ie, Sectors)
     *  second argument : name of intermediate model
     *  third argument : name of foreign key on intermediate model
     *  fourth argument : name of foreign key on final model
     * 
     */
    public function sector()
    {
       return $this->hasOneThrough('App\Models\StockSector','App\Models\Stock','id','id');
    }   
    
}
