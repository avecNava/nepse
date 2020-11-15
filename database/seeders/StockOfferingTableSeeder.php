<?php

namespace Database\Seeders;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class StockOfferingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('stock_offerings')->truncate();
        $offers = [
            'IPO' => 'Initial Public offering',
            'FPO' => 'Fixed Premium offering',
            'BONUS' => 'Bonus share',
            'RIGHTS' => 'Rights share',
            'IPO-PREMIUM' => 'IPO premium',
            'Bonds' => 'Bonds',
        ];
        foreach ($offers as $key => $value) {
            \DB::table('share_offerings')->insert([
                'offer_name' => $key,
                'offer_description' => $value,
                'created_at' => Carbon::now()->toDateTimeString()
                ]);
        }     
    }
}