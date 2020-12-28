<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Login extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $guarded = [];

    public static function CreateLoginRecords(Request $request)
    {
        $agent = new Agent();

        $device_type = 'na';
        if($agent->isDesktop()){
            $device_type  = 'Desktop';
        }
        elseif($agent->isPhone()){
            $device_type = 'Phone';
        }
        else{
            $device_type = 'Other';
        }
        
        $browser_version = explode('.', $agent->version($agent->browser()))[0];
        $platform_version = explode('.', $agent->version($agent->platform()))[0];
        // 'user_agent' => $request->server('HTTP_USER_AGENT'),

        LogIn::Create([
            'ip' => $request->ip(),
            'device' => $agent->device(),
            'device_type' => $device_type,
            'robot' => $agent->isRobot(),
            'browser' => $agent->browser(),
            'browser_version' => $browser_version,
            'platform' => $agent->platform(),
            'platform_version' => $platform_version,
            'user_id' => Auth::id(),
        ]);

    }
}
