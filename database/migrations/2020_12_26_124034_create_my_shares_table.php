<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMySharesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('my_shares', function (Blueprint $table) {
            $table->id();
            $table->string('symbol');
            $table->integer('quantity');
            $table->integer('unit_cost')->nullable();
            $table->float('effective_rate',8,2)->nullable();
            $table->string('offer_code');
            $table->text('description')->nullable();
            $table->foreignId('shareholder_id')->constrained('shareholders')->onDelete('cascade');
            $table->date('purchase_date')->nullable();
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
        Schema::dropIfExists('my_shares');
    }
}
