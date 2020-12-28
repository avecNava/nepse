<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $guarded = [];

    public static function CreateLogRecords(Request $request)
    {
        

        // Log::Create([
        
        //     'user_id' => Auth::id(),
        // ]);

    }
}
