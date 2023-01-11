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
        Schema::table('cvs', function (Blueprint $table) {
            $table->string('type')->nullable()->change();
            $table->integer('office_type')->nullable()->change();
            $table->boolean('is_ready_to_relocate')->nullable()->change();
            $table->integer('minimal_salary')->nullable()->change();
            $table->integer('desired_salary')->nullable()->change();
            $table->string('currency')->nullable()->change();
            $table->string('status')->nullable()->change();
            $table->string('position')->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cvs', function (Blueprint $table) {
            $table->string('type')->change();
            $table->integer('office_type')->change();
            $table->boolean('is_ready_to_relocate')->change();
            $table->integer('minimal_salary')->change();
            $table->integer('desired_salary')->change();
            $table->string('currency')->change();
            $table->string('status')->change();
            $table->string('position')->change();

        });
    }
};
