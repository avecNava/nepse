<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePortfolioSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('portfolio_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shareholder_id')->constrained('shareholders')->onDelete('cascade');
            $table->foreignId('stock_id')->constrained('stocks');
            $table->integer('total_quantity');
            $table->float('wacc_rate',8,2)->nullable();
            $table->foreignId('last_modified_by')->nullable();
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
        Schema::dropIfExists('portfolio_summaries');
    }
}
