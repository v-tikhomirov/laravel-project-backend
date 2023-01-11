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
        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id');
            $table->string('slug');
            $table->string('position');
            $table->string('currency');
            $table->integer('desired_salary');
            $table->integer('max_salary');
            $table->integer('office_type');
            $table->boolean('is_ready_to_relocate')->default(0);
            $table->string('relocation_benefits')->nullable();
            $table->integer('country_id');
            $table->integer('city_id');
            $table->text('description');
            $table->integer('status');
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
        Schema::dropIfExists('vacancies');
    }
};
