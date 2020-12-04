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
            $table->foreignId('portfolio_id')->constrained('portfolios');
            $table->date('sales_date')->nullable();
            $table->string('receipt_number');
            $table->float('sales_amount', 8, 2)->nullable();
            $table->float('net_gain', 8, 2)->nullable();
            $table->float('net_gain_per', 5, 2)->nullable();
            $table->foreignId('last_updated_by')->constrained('user');
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
