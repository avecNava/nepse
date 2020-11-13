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
        Schema::create('stock_categories', function (Blueprint $table) {
            // $table->autoIncrement('category_id');
            $table->id();
            $table->string('sector');
            $table->string('sub_sector');
            $table->timestamps();
        });
       
       Schema::create('stocks', function (Blueprint $table) {
            $table->id()->from(100);
            $table->string('symbol');
            $table->string('security_name');
            $table->foreignId('category_id')->nullable();
            $table->softDeletesTz('deleted_at', 0);
            $table->foreignId('user_id');
            $table->timestamps();
        });
        
        Schema::create('stock_prices', function (Blueprint $table) {
            $table->id();
            $table->string('stock_id')->nullable();
            $table->string('symbol');
            $table->string('security_name');

            $table->string('open_price');
            $table->string('high_price');
            $table->string('low_price');
            $table->string('close_price')->nullable();
            $table->string('previous_day_close_price');

            $table->string('total_traded_qty');
            $table->string('total_traded_value');
            $table->string('total_trades');

            $table->string('avg_traded_price');
            $table->string('fifty_two_week_high_price');
            $table->string('fifty_two_week_low_price');

            // $table->string('total_buy_qty');
            // $table->string('total_sell_qty');
            $table->string('last_updated_price');
            //date
            $table->string('last_updated_time')->nullable();
            $table->string('transaction_date');

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
        Schema::dropIfExists('stock_categories');
        Schema::dropIfExists('stocks');
        Schema::dropIfExists('stock_prices');
    }
}
