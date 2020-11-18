<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shareholder extends Model
{
    use HasFactory;

    public function shares()
    {
        return $this->belongsTo('App\Models\Portfolio');
    }
}
