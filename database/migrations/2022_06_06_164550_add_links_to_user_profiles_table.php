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
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string('link_to_linkedin')->nullable()->after('native_language_id');
            $table->string('link_to_github')->nullable()->after('native_language_id');
            $table->string('link_to_medium')->nullable()->after('native_language_id');
            $table->string('link_to_youtube')->nullable()->after('native_language_id');
            $table->string('link_to_stackoverflow')->nullable()->after('native_language_id');
            $table->string('link_to_facebook')->nullable()->after('native_language_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn('link_to_linkedin');
            $table->dropColumn('link_to_github');
            $table->dropColumn('link_to_medium');
            $table->dropColumn('link_to_youtube');
            $table->dropColumn('link_to_stackoverflow');
            $table->dropColumn('link_to_facebook');
        });
    }
};
