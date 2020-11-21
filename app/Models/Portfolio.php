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
}
