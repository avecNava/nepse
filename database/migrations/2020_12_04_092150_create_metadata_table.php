<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetadataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

     //todo : add other metadata reqd for data analsis
     
    public function up()
    {
        Schema::create('metadata', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_id')->references('stocks');
            $table->int('customers')->nullable();
            $table->unsignedBigInteger('capital')->nullable();
            $table->float('eps',5,2)->nullable();
            $table->float('eps_q1',5,2)->nullable();
            $table->float('eps_q2',5,2)->nullable();
            $table->float('eps_q3',5,2)->nullable();
            $table->float('eps_q4',5,2)->nullable();
            $table->unsignedBigInteger('profit')->nullable();
            $table->unsignedBigInteger('profit_q1')->nullable();
            $table->unsignedBigInteger('profit_q2')->nullable();
            $table->unsignedBigInteger('profit_q3')->nullable();
            $table->unsignedBigInteger('profit_q4')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
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
        Schema::dropIfExists('metadata');
    }
}
