<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class StockCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        \DB::table('stock_categories')->truncate();
        $sectors = ['Hyrdo','Banks','Microfinance','Production','Hotel','Other'];
        foreach ($sectors as $value) {
            \DB::table('stock_categories')->insert([
                'sector' => $value,
                'sub_sector' => $value,
                'created_at' => Carbon::now()->toDateTimeString()
                ]);
        }         
    }
}
