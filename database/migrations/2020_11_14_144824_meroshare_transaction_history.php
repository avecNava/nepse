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
            $table->foreignId('shareholder_id')->nullable();
            $table->string('symbol');
            $table->integer('credit_quantity')->nullable();
            $table->integer('debit_quantity')->nullable();
            $table->string('offer_code');
            $table->string('transaction_mode');
            $table->date('transaction_date')->nullable();
            $table->string('remarks')->nullable();
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
