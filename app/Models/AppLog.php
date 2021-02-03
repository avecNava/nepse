<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AppLog extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $guarded = [];
    protected $table = 'logs';

    public static function createLog($arr_log)
    {
        
        Log::info($arr_log['title'], 
        [
            'Description' => $arr_log['desc'],
        ]);

        AppLog::create([
            'model' =>  $arr_log['module'],
            'title' => $arr_log['title'],
            'description' => $arr_log['desc'],
            'user_id' => Auth::id(),
        ]);

    }
}
