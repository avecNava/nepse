<?php
namespace App\Services;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UtilityService
{
    public static function generateToken()
    {
        $token = Hash::make(Carbon::now()->toDateTimeString());
        $token = Str::of($token)->replaceMatches('/[.-\/]+/','_');
        // echo Str::length($token); echo '<br>';
        return $token;

    }

    public static function serializeTime()
    {
        $time = Carbon::now();
        return Str::of( $time )->replaceMatches('/[ :-]+/','');
    
    }
    
    public static function serializeString($name)
    {
        return Str::of( $name )->replaceMatches('/[ :-]+/','');    
    }

    /**
     * checks if the given day is working day or not
     */
    public static function tradingDay($date)
    {
        //todo: read working days from ENV
        $day = Str::lower( Carbon::parse($date)->format("l") );
        
        $trading_days = ['sunday','monday','tuesday','wednesday','thursday'];
        if(in_array($day, $trading_days)) {
            return true;
        }
        return false;
    }
    
    /**
     * checks if the given day is working day or not
     */
    public static function governmentHoliday()
    {
        //todo: check if given date is government holiday
        return false;
    }
}