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

    /**
     * Broker Commission rate
     */
    public static function commission()  
    {
        $rate = collect([
            [
                'alias'=>'upto50K',
                'amount_fl' => 1,
                'amount' => 50000,
                'broker' =>0.6,
            ],
            [
                'alias'=>'upto5L',
                'amount_fl' => 50000,
                'amount' => 500000,
                'broker' =>0.55,
            ],
            [
                'alias'=>'upto20L',
                'amount_fl' => 500000,
                'amount' => 2000000,
                'broker' =>0.5,
            ],
            [
                'alias'=>'upto1Cr',
                'amount_fl' => 200000,
                'amount' => 10000000,
                'broker' =>0.45,
            ],
            [
                'alias'=>'beyond1Cr',
                'amount_fl' => 10000000,
                'amount' => 10000000,
                'broker' =>0.4,
            ],
        ]);

        return $rate;
    }
}