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
            StockOffersTableSeeder::class,
            StockCategoriesTableSeeder::class,
            RelationsTableSeeder::class,
            // ShareholderTableSeeder::class,
            // StockTableSeeder::class,
        ]);
    }
}
