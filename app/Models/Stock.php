<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;
    protected $fillable = ['symbol','security_name'];

    // public function belongsTo()
    // {
    //     return $this->hasMany('App\Models\Portfolio','symbol','symbol');
    // }

    public static function addOrUpdateStock(Array $stocks)
    {
        if( empty($stocks) ) return false;
        
        foreach ($stocks as $data) {
            
            Stock::updateOrCreate(
                [
                    'symbol' => $data['symbol'],
                    'security_name' => $data['securityName'] 
                ]
            );
                
        }
    }
}
