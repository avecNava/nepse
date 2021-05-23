<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DailyIndex;

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

    /**gets the last index and updates the NEPSEIndex table */
    public static function updateCurrentIndex()
    {
        
        $row = DailyIndex::orderByDesc('epoch')->first();
        
        if($row){

            $date_time = new \DateTime($row['transactionDate']);
            $businessDate = $date_time->format('Y-m-d');
            NepseIndex::updateOrCreate(
                ['transactionDate' => $businessDate],
                ['closingIndex' => $row['index']],
            );            
        }
    }

}
