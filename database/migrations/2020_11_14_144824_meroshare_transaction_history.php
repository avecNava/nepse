<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MeroshareTransactionHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meroshare_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('symbol');
            $table->integer('credit_quantity')->nullable();
            $table->integer('debit_quantity')->nullable();
            $table->string('offering_type');
            $table->string('transaction_mode');
            $table->string('remarks')->nullable();
            $table->foreignId('shareholder_id')->nullable();
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
        Schema::dropIfExists('meroshare_transactions');
    }
}
