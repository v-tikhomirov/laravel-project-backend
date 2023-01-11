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
        Schema::create('cvs', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->unsignedInteger('user_id');
            $table->integer('office_type');
            $table->boolean('is_ready_to_relocate');
            $table->boolean('is_show_workplaces');
            $table->integer('minimal_salary');
            $table->integer('desired_salary');
            $table->string('currency');
            $table->integer('status');
            $table->string('position');
            $table->text('about')->nullable();
            $table->string('link_to_linkedin')->nullable();
            $table->string('link_to_github')->nullable();
            $table->string('link_to_medium')->nullable();
            $table->string('link_to_youtube')->nullable();
            $table->string('link_to_stackoverflow')->nullable();
            $table->string('link_to_facebook')->nullable();
            $table->boolean('is_published');
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
        Schema::dropIfExists('cvs');
    }
};
