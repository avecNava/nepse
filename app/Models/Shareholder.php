<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public static function getShareholderNames($parent_id)
    {
        $shareholders = Shareholder::where('parent_id', $parent_id)->get();
        $shareholder = $shareholders->map(function($item, $key){
            return collect([
                'name' => "$item->first_name $item->last_name",
                'relation' => !empty($item->relation) ? "($item->relation)":'(Self)',
                'id' => $item->id,
            ]);

        });
        return $shareholder;
    }
}
