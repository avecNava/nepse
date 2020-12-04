<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //todo:
        //events (IPO/FPO/BONUS etc)
        //news 
              
       Schema::create('stocks', function (Blueprint $table) {
            $table->id()->from(100);
            $table->string('symbol');
            $table->string('security_name');
            $table->boolean('active')->default(true);
            $table->foreignId('sector_id')->constrained()->nullable();
            $table->foreignId('last_updated_by')->nullable();           //userid
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
        Schema::dropIfExists('stocks');
    }
}
