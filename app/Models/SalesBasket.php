<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Traits\BelongsToTenant;

class SalesBasket extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'sales_basket';
    protected $guarded = [];

    // public function portfolio()
    // {
    //     return $this->hasMany('App\Models\PortfolioSummary', 'stock_id','stock_id');
    // }
    
    public function shareholder()
    {
        return $this->belongsTo('App\Models\Shareholder', 'shareholder_id');
    }

    public function share()
    {
        return $this->belongsTo('App\Models\Stock', 'stock_id');
    }
    
    public function price()
    {
        return $this->belongsTo('App\Models\StockPrice', 'stock_id','stock_id')->where('latest', true);
    }

}
