<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePortfoliosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->foreignId('shareholder_id')->nullable();
            $table->foreignId('stock_id')->nullable();
            $table->integer('quantity');
            // $table->float('unit_cost',8,2)->nullable();
            // $table->float('total_amount',8,2)->nullable();
            // $table->float('rate',8,2)->nullable();
            // $table->float('effective_rate',8,2)->nullable();
            // $table->float('broker_commission',8,2);
            // $table->float('broker_commission_per',8,2);
            // $table->float('sebon_commission',8,2);
            // $table->float('dp_amount',8,2);
            // $table->float('name_transfer',8,2);
            // $table->foreignId('stock_category_id')->nullable();
            $table->foreignId('stock_offer_id')->nullable();
            $table->foreignId('group_id')->nullable();
            $table->date('purchase_date')->nullable();
            // $table->string('purchase_number');
            // $table->integer('broker_no');
            $table->foreignId('created_by')->nullable();
            $table->foreignId('last_updated_by')->nullable();
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
        Schema::dropIfExists('portfolios');
    }
}
