<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RelationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('relations')->truncate();
        $relations = ['Father','Mother','Son','Daughter','Brother','Sister','Husband','Wife','Cousin','Nephew','Aunt','Uncle','Friend','Partnership','Other','Grand father','Grand mother'];
        foreach ($relations as $value) {
            \DB::table('relations')->insert([
                'relation' => $value,
                'created_at' => Carbon::now()->toDateTimeString()
            ]);
        }      
    }
}
