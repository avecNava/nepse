<?php

namespace Database\Seeders;

use App\Models\Shareholder;
use Illuminate\Database\Seeder;

class ShareholderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('shareholders')->truncate();
        Shareholder::factory()->count(10)->create();
    }
}
