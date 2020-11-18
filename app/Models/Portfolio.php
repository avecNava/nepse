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
        return $this->hasMany('App\Models\Shareholder','user_id');
    }

    public function shares()
    {
        return $this->hasMany('App\Models\Stock','symbol','symbol');
    }
}
