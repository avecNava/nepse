<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class StockSectorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        \DB::table('stock_sectors')->delete();
        // \DB::table('stock_sectors')->truncate();
        $sectors = [
            'Hydropower',
            'Corporate Debenture',
            'Hotels',
            'Commercial Banks',
            'Development Banks',
            'Life Insurance',
            'Non life Insurance',
            'Microfinance',
            'Finance',
            'Production',
            'Hotel',
            'Investment Fund',
            'Manufacturing & Processing',
            'Tradings',
            'Preferred Stocks',
            'Others'
        ];

        foreach ($sectors as $value) {
            \DB::table('stock_sectors')->insert([
                'sector' => $value,
                'sub_sector' => $value,
                'created_at' => Carbon::now()->toDateTimeString()
                ]);
        }         
    }
}
