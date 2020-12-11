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
            'Insurance - life',
            'Insurance- Non life',
            'Microfinance',
            'Finance',
            'Production',
            'Hotel',
            'Investment fund',
            'Manufacturing And Processing',
            'Tradings',
            'Preferred Stocks',
            'Other'
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
