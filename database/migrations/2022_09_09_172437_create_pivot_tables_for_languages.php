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
        Schema::create('vacancy_language', function (Blueprint $table) {
            $table->unsignedBigInteger('vacancy_id');
            $table->unsignedBigInteger('language_id');
            $table->string('level');
        });

        Schema::create('cv_language', function (Blueprint $table) {
            $table->unsignedBigInteger('cv_id');
            $table->unsignedBigInteger('language_id');
            $table->string('level');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vacancy_language');
        Schema::dropIfExists('cv_language');
    }
};
