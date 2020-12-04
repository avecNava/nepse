<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTiersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('tier_id')->constrained('tiers');
            $table->date('enroll_date');
            $table->date('expiry_date');
            $table->string('mode_payment')->nullable();         //cash, voucher, card, payment gateway
            $table->string('receipt_number')->nullable();
            $table->float('bill_amount',8,2)->nullable();
            $table->bit('confirmation')->default(0);
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
        Schema::dropIfExists('user_tiers');
    }
}
