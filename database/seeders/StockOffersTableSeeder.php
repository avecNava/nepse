<?php

namespace Database\Seeders;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class StockOffersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //\DB::table('stock_offers')->truncate();
        \DB::table('stock_offers')->delete();

        $offers = [
            'IPO' => 'Initial Public offering',
            'FPO' => 'Fixed Premium offering',
            'BONUS' => 'Bonus share',
            'MUTUALFUND' => 'Mutual funds',
            'RIGHTS' => 'Rights share',
            'IPO1' => 'IPO premium',
            'SECONDARY' => 'Secondary market (Broker)',
            'BONDS' => 'Bonds',
            'SALES' => 'Sales',
            'OTHERS' => 'Others',
        ];

        foreach ($offers as $key => $value) {
            \DB::table('stock_offers')->insert([
                'offer_code' => $key,
                'offer_name' => $value,
                'created_at' => Carbon::now()->toDateTimeString()
                ]);
        }     
    }
}