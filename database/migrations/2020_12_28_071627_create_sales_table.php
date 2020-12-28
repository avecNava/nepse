<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shareholder_id')->constrained('shareholders')->onDelete('cascade');
            $table->foreignId('stock_id')->constrained('stocks')->onDelete('cascade');
            $table->date('sales_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->integer('quantity');
            $table->float('wacc', 8, 2)->nullable();
            $table->float('sales_amount', 12, 2)->nullable();
            $table->float('gain', 8, 2)->nullable();
            $table->float('gain_tax', 5, 2)->nullable();
            $table->float('broker_commission',8,2)->nullable();
            $table->float('sebon_commission',8,2)->nullable();
            $table->float('dp_amount', 5, 2)->default(25);
            $table->string('remarks',500)->nullable();
            $table->integer('tenant_id')->index();
            // $table->foreignId('portfolio_id')->nullable()->constrained('portfolios')->onDelete('cascade');
            // $table->foreignId('offer_id')->nullable()->constrained('stock_offerings');
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
        Schema::dropIfExists('sales');
    }
}