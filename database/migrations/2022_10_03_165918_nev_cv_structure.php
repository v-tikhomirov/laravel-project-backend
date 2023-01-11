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
            $table->dropColumn('is_show_workplaces');
            $table->dropColumn('link_to_linkedin');
            $table->dropColumn('link_to_github');
            $table->dropColumn('link_to_medium');
            $table->dropColumn('link_to_youtube');
            $table->dropColumn('link_to_stackoverflow');
            $table->dropColumn('link_to_facebook');
            $table->dropColumn('is_published');

            $table->string('type')->after('user_id');
            $table->boolean('is_draft')->after('about')->default(0);

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
            $table->boolean('is_show_workplaces');
            $table->string('link_to_linkedin')->nullable();
            $table->string('link_to_github')->nullable();
            $table->string('link_to_medium')->nullable();
            $table->string('link_to_youtube')->nullable();
            $table->string('link_to_stackoverflow')->nullable();
            $table->string('link_to_facebook')->nullable();
            $table->boolean('is_published');
        });
    }
};
