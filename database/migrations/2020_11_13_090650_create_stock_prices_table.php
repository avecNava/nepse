<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_prices', function (Blueprint $table) {

            $table->id();
            $table->foreignId('stock_id')->nullable();
            $table->string('symbol')->index();

            $table->float('open_price',8,2)->nullable();
            $table->float('high_price',8,2)->nullable();
            $table->float('low_price',8,2)->nullable();
            $table->float('close_price',8,2)->nullable();
            $table->float('last_updated_price',8,2)->nullable();
            $table->float('previous_day_close_price',8,2);

            $table->unsignedMediumInteger('total_traded_qty')->nullable();
            $table->unsignedInteger('total_traded_value')->nullable();
            $table->unsignedMediumInteger('total_trades')->nullable();

            $table->double('avg_traded_price',8,2)->nullable();
            $table->double('fifty_two_week_high_price',8,2)->nullable();
            $table->double('fifty_two_week_low_price',8,2)->nullable();

            // $table->string('last_updated_time')->nullable();
            $table->datetime('last_updated_time')->nullable();
            $table->date('transaction_date');
            $table->boolean('latest')->default(false);

            $table->string('source')->default('nepalstock');
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
        Schema::dropIfExists('stock_prices');
    }
}
