<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesBasketTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_basket', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shareholder_id')->constrained('shareholders')->onDelete('cascade');
            $table->foreignId('stock_id')->constrained('stocks')->onDelete('cascade');
            $table->datetime('basket_date')->nullable();
            $table->integer('quantity');
            $table->float('wacc', 8, 2)->nullable();
            $table->float('sebon_commission', 8, 2)->nullable();
            $table->float('broker_commission', 8, 2)->nullable();
            $table->integer('dp_amount')->default(25);
            $table->float('capital_gain_tax', 8, 2)->nullable();
            $table->float('cost_price', 14, 2)->nullable();
            $table->float('sell_price', 14, 2)->nullable();
            $table->float('net_receivable', 14, 2)->nullable();
            $table->integer('tenant_id')->index();
            $table->foreignId('portfolio_id')->constrained('portfolios')->nullable();
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
        Schema::dropIfExists('sales_basket');
    }
}
