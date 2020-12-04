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
            $table->foreignId('shareholder_id')->constrained('shareholders');
            $table->foreignId('stock_id')->constrained('stocks');
            $table->float('unit_cost',8,2)->nullable();
            $table->integer('quantity');
            $table->float('total_amount',8,2)->nullable();
            $table->float('effective_rate',8,2)->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('sales_date')->nullable();
            $table->string('receipt_number')->nullable();
            $table->foreignId('offer_id')->constrained('stock_offers')->nullable()->onDelete('cascade');
            $table->text('remarks')->nullable();
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
