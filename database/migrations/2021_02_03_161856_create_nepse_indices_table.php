<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNepseIndicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nepse_indices', function (Blueprint $table) {
            $table->id();
            $table->date('businessDate');
            $table->float('closingIndex',8,4)->nullable();
            $table->float('openIndex',8,4)->nullable();
            $table->float('highIndex',8,4)->nullable();
            $table->float('lowIndex',8,4)->nullable();
            $table->float('fiftyTwoWeekHigh',8,4)->nullable();
            $table->float('fiftyTwoWeekLow',8,4)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nepse_indices');
    }
}
