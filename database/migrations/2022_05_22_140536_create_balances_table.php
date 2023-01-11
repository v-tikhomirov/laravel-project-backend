<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('balances', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id');
            $table->double('amount')->default(0);
            $table->string('currency')->default('USD');
            $table->tinyInteger('discount')->comment('Discount in percentage')->nullable();
            $table->tinyInteger('discount_type')->default(0);
            $table->dateTime('available_from')->nullable();
            $table->dateTime('available_to')->nullable();
            $table->integer('qty')->nullable();
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
        Schema::dropIfExists('balances');
    }
};
