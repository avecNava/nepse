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
            'RIGHT' => 'Rights share',
            'PREMIUM' => 'IPO premium',
            'SECONDARY' => 'Broker',
            'OTC' => 'Over the Counter',
            'AUCTION' => 'Auction',
            'BOND' => 'Bonds',
            // 'SALES' => 'Sales',
            'OTHER' => 'Others',
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