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
}