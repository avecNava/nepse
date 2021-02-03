<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class NepseIndex extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $guarded = [];


    public static function getCurrentIndex()
    {
        
        $time_start = Carbon::now();
        $date_string =  $time_start->toDateString();

        $row = NepseIndex::where('businessDate', $date_string)->first();
        return $row;

    }
}
