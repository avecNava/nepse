<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class StockTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // \DB::table('stocks')->truncate();
        $stocks = [
            array(
                'symbol' => 'CHCL',
                'script_name' => 'Chilime Hydropower',
            ),
            array(
                'symbol' => 'API',
                'script_name' => 'API Hydro',
            ),
            array(
                'symbol' => 'FMDBL',
                'script_name' => 'FMDBL',
            ),
            array(
                'symbol' => 'NGBBL',
                'script_name' => 'Nepal Grameen Bikas Laghubitta',
            ),
            array(
                'symbol' => 'SICL',
                'script_name' => 'Shikhar Insurance',
            ),
        ];

        foreach ($stocks as $stock) {
            \DB::table('stocks')->insert([
                'symbol' => $stock['symbol'],
                'security_name' => $stock['script_name'],
                'created_at' => Carbon::now()->toDateTimeString()
            ]);
        }
    }
}
