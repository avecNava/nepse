<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShareholderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shareholders', function (Blueprint $table) {
            $table->id();
            $table->integer('parent_id')->nullable();
            $table->boolean('group')->nullable();
            $table->string('title')->nullable();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('alias')->nullable();
            $table->string('gender')->nullable();
            $table->string('email')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('relation')->nullable();
            $table->boolean('parent')->default(false);
            $table->integer('tenant_id')->index();
            $table->uuid('uuid')->index();

            // $table->foreignId('user_id');
            // $table->foreignIdFor(model: \App\Models\User::class);
            //https://laravel.com/docs/8.x/migrations#foreign-key-constraints
            $table->foreignId('last_modified_by')->constrained('users')->nullable();    //references id on users
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
        Schema::dropIfExists('shareholders');
    }
}
