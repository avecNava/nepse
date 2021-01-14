<?php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
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

    /**
     * calculates the percentage
     */
    public static function calculatePercentage($value, $total_value)
    {
        if($total_value<=0) return 0;
        return number_format(($value/$total_value)*100,1).'%';
    }

    /**
     * calculates gain or loss based on give value
     */
    public static function gainLossClass($value)
    {
        
        if(!$value) return '';

        $class = '';

        if($value > 0)  {
            $class = 'positive';
         }
         else if($value < 0){
             $class = 'negative';
         }
         return $class;
    }
    
    public static function gainLossClass1($value)
    {
        
        if(!$value) return '';

        $class = '';

        if($value > 0)  {
            $class = 'increase';
         }
         else if($value < 0){
             $class = 'decrease';
         }
         return $class;
    }
    
    public static function serializeString($name, $delim='')
    {
        return Str::lower(Str::of( $name )->replaceMatches('/[ :_]+/', $delim));    
    }
    public static function serializeNames($fname, $lname, $delim='-')
    {
        return Str::lower($fname) . $delim . Str::lower($lname);
    }

    public static function parseFirstName($input)
    {
        if(UtilityService::IsNullOrEmptyString($input))
            return "Guest";

        $temp = explode(" ", $input);
        return $temp[0];
    }

    /**
     * returns if the URL matches the given key
     */
    public static function urlMatch($text)
    {
        $url = Str::lower(url()->current());
        return Str::endsWith($url, Str::lower($text)) ? true : false;

    }

    /**
     * check if given string is null or empty
     */
    public static function IsNullOrEmptyString($question){
        return (!isset($question) || trim($question)==='');
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
                'label'=>'upto50K',
                'min_amount' => 1,
                'max_amount' => 50000,
                'broker' => 0.6,
                'sebon' => 0.015,
            ],
            [
                'label'=>'upto5L',
                'min_amount' => 50000,
                'max_amount' => 500000,
                'broker' => 0.55,
                'sebon' => 0.015,
            ],
            [
                'label'=>'upto20L',
                'min_amount' => 500000,
                'max_amount' => 2000000,
                'broker' => 0.5,
                'sebon' => 0.015,
            ],
            [
                'label'=>'upto1Cr',
                'min_amount' => 200000,
                'max_amount' => 10000000,
                'broker' => 0.45,
                'sebon' => 0.015,
            ],
            [
                'label'=>'beyond1Cr',
                'min_amount' => 10000000,
                'max_amount' => 10000000,
                'broker' => 0.4,
                'sebon' => 0.015,
            ],
        ]);

        return $rate;
    }

    
    /**
     * create log
     */
    public static function createLog($message, $arr, $type='exception')
    {
        $obj = [
            'message' => $arr->getMessage(),
            'line' => $arr->getLine(),
            'file' => $arr->getFile(),
        ];
        if (Str::contains(Str::lower($type), 'exception')){
            Log::error("ERROR: $message", [$obj]);
        }
        else {
            Log::info("MESSAGE: $message ", [$obj]);
        }
    }
}