<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            StockOfferingsTableSeeder::class,
            StockSectorsTableSeeder::class,
            RelationsTableSeeder::class,
            // ShareholderTableSeeder::class,
            // StockTableSeeder::class,
        ]);
    }
}
