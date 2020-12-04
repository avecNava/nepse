<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWatchlistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('watchlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shareholder_id')->constrained('shareholders');
            $table->foreignId('stock_id')->constrained('stocks');
            $table->foreignId('created_by')->constrained('users');
            $table->bit('trigger')->default(1);
            $table->float('low_amount',8,2)->nullable();
            $table->float('high_amount',8,2)->nullable();
            $table->string('notification_mode')->default('email');
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
        Schema::dropIfExists('watchlists');
    }
}
