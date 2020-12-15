<?php

namespace Database\Seeders;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class StockOfferingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //\DB::table('stock_offerings')->truncate();
        \DB::table('stock_offerings')->delete();

        $offers = [
            'IPO' => 'Initial Public offering',
            'FPO' => 'Fixed Premium offering',
            'BONUS' => 'Bonus share',
            'MUTUALFUND' => 'Mutual funds',
            'RIGHTS' => 'Rights share',
            'PREMIUM' => 'IPO premium',
            'SECONDARY' => 'Broker',
            'OTC' => 'Over the Counter',
            'AUCTION' => 'Auction',
            'BONDS' => 'Bonds',
            'SALES' => 'Sales',
            'OTHERS' => 'Others',
        ];

        foreach ($offers as $key => $value) {
            \DB::table('stock_offerings')->insert([
                'offer_code' => $key,
                'offer_name' => $value,
                'created_at' => Carbon::now()->toDateTimeString()
                ]);
        }     
    }
}