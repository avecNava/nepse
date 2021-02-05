<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NepseIndex extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $guarded = [];


    public static function getCurrentIndex()
    {
        
        $businessDate =  NepseIndex::max('transactionDate');
        $row = NepseIndex::where('transactionDate', $businessDate)->first();
        return $row;

    }
}
