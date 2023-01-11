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
        Schema::table('companies', function (Blueprint $table) {
            $table->text('about')->after('website')->nullable();
            $table->string('link_to_linkedin')->nullable()->after('city');
            $table->string('link_to_github')->nullable()->after('city');
            $table->string('link_to_medium')->nullable()->after('city');
            $table->string('link_to_youtube')->nullable()->after('city');
            $table->string('link_to_stackoverflow')->nullable()->after('city');
            $table->string('link_to_facebook')->nullable()->after('city');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('about');
            $table->dropColumn('link_to_linkedin');
            $table->dropColumn('link_to_github');
            $table->dropColumn('link_to_medium');
            $table->dropColumn('link_to_youtube');
            $table->dropColumn('link_to_stackoverflow');
            $table->dropColumn('link_to_facebook');
        });
    }
};
