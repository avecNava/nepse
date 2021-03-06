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
            $table->foreignId('shareholder_id')->constrained('shareholders')->onDelete('cascade');
            $table->foreignId('stock_id')->constrained('stocks');
            $table->float('unit_cost',8,2)->nullable();
            $table->float('effective_rate',8,2)->nullable();
            $table->integer('quantity');
            $table->float('broker_commission',8,2)->nullable();
            $table->float('sebon_commission',8,2)->nullable();
            $table->float('dp_amount',8,2)->nullable();
            $table->float('base_amount',15,2)->nullable();             //extremely high value
            $table->float('total_amount',15,2)->nullable();             //extremely high value
            $table->foreignId('offer_id')->nullable()->constrained('stock_offerings');
            // $table->foreignId('offer_id')->constrained('stock_offerings')->nullable()->onDelete('cascade');
            $table->date('purchase_date')->nullable();
            $table->integer('broker_no')->nullable();
            $table->string('receipt_number')->nullable();
            $table->string('tags')->nullable();
            $table->datetime('wacc_updated_at')->nullable();
            $table->string('remarks',500)->nullable();
            $table->integer('tenant_id')->index();
            $table->foreignId('last_modified_by')->constrained('users')->nullable();
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
