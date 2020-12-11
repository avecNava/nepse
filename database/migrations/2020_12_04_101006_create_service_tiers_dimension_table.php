<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceTiersDimensionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('tier_name');
            $table->string('tier_type');                //free, silver, gold, lifetime
            $table->float('tier_price',8,2);
            $table->datetime('last_updated');
            $table->foreignId('last_modified_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_tiers');
    }
}
