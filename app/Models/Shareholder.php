<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Shareholder extends Model
{
    use HasFactory;
    protected $fillable = [
        'first_name',
        'last_name',
        'parent_id',
        'relation',
        'gender',
        'email',
        'date_of_birth',
        'user_id'
    ];

    public function shares()
    {
        return $this->belongsTo('App\Models\Portfolio');
    }

    public static function createShareholder(Request $request)
    {
        Shareholder::updateOrCreate(
            [
                'id' => $request->id
            ],
            [
                'user_id' => Auth::id(),
                'parent_id' => Auth::id(),
                'first_name' => Str::title($request->first_name),
                'last_name' => Str::title($request->last_name),
                'email' => $request->email,
                'date_of_birth' => $request->date_of_birth,
                'gender' => Str::title(Str::substr($request->gender,0,1)),
                'relation' => Str::title($request->relation),
            ]
        );
    }

    public static function getShareholderNames($parent_id)
    {
        $shareholders = Shareholder::where('parent_id', $parent_id)->get();
        $shareholder = $shareholders->map(function($item, $key){
            return collect([
                'name' => "$item->first_name $item->last_name",
                'relationF' => !empty($item->relation) ? "($item->relation)":'(Self)',
                'relation' => $item->relation,
                'date_of_birth' => $item->date_of_birth,
                'gender' => $item->gender,
                'email' => $item->email,
                'id' => $item->id,
            ]);

        });
        return $shareholder;
    }
}
