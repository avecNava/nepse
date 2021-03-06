<?php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Auth;

class UtilityService
{
    public static function getNotice()
    {
       
        //no message if flag set in ENV
        if(!config('app.message'))       
            return "";          

        // $name = UtilityService::parseFirstName(optional(Auth::user())->name);
        
        $notice = 'We have revamped the website. The old site has been moved <a href="http://old.nepse.today"target="_blank" rel="noopener noreferrer">here</a>';
        $notice = "<strong>This website is a work in progress.</strong> If you have any issues or feedbacks you can reach us via filling the <a href='/feedbacks'>feedback form</a>.";
        return $notice;
    }

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
        return number_format(($value/$total_value)*100, 2).'%';
    }

    /**
     * calculates gain or loss based on give value
     */
    public static function gainLossClass($value)
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
    
    public static function cleanString($name)
    {
        return Str::lower(Str::of( $name )->replaceMatches('/[\/ :_]+/', ""));    
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
                'broker' => 0.4,
                'sebon' => 0.015,
            ],
            [
                'label'=>'upto5L',
                'min_amount' => 50000,
                'max_amount' => 500000,
                'broker' => 0.37,
                'sebon' => 0.015,
            ],
            [
                'label'=>'upto20L',
                'min_amount' => 500000,
                'max_amount' => 2000000,
                'broker' => 0.34,
                'sebon' => 0.015,
            ],
            [
                'label'=>'upto1Cr',
                'min_amount' => 200000,
                'max_amount' => 10000000,
                'broker' => 0.3,
                'sebon' => 0.015,
            ],
            [
                'label'=>'beyond1Cr',
                'min_amount' => 10000000,
                'max_amount' => 10000000,
                'broker' => 0.27,
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

    public static function epochToTimeZone($epoch, $timezone)
    {
        // date_default_timezone_set('UTC');
        $datetime_str = date("Y-m-d H:i:s",substr($epoch, 0, 10));
        $datetime = new \DateTime($datetime_str);
        $datetime->setTimezone( new \DateTimeZone($timezone) );
        return $datetime->format('Y-m-d H:i:s');
        // return $datetime;
    }

    /*
    * returns date part from date time string
    * input date_string
    */
    public static function getDateFromString($date_string){
        $display_time = new \DateTime($date_string);
        return $display_time->format('Y-m-d');
    }

    /*
    * converts epoch to date
    * input : epoch
    *  the epoch is Unix time 0 (midnight 1/1/1970) and length is of 10 characters
    * //sample epoch 
    * // [
    * //     1621746300
    * // ],
    */
    public static function getDateFromEpoch($epoch)
    {
        try {
            $index_date = new \DateTime("@$epoch");
            $time_string = $index_date->format('Y-m-d H:i:s');
            $display_time = new \DateTime($time_string, new \DateTimeZone('UTC'));
            $display_time->setTimeZone(new \DateTimeZone('Etc/GMT-6'));             //Ideally the timezone should be Asia/Kathmandu
            // return $display_time->format('Y-m-d H:i:s');
            return $display_time->format('Y-m-d');

        } catch (\Throwable $th) {
            return response()->json( ['message'=> $th->getMessage()] );
        }
    }

    /**
     * format money to millions, thousands, hundreds etc
     */
    public static function formatMoney($number)
    {
        if ($number < 1000000) {
            // Anything less than a million
            $format = number_format($number);
        } else if ($number < 1000000000) {
            // Anything less than a billion
            $format = number_format($number / 1000000, 2) . 'M';
        } else {
            // At least a billion
            $format = number_format($number / 1000000000, 2) . 'B';
        }
        return $format;
    }
}