<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInfoAboutLocationToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('location');

            $table->string('lat')->nullable()->after('email');
            $table->string('lon')->nullable()->after('lat');
            $table->string('countryCode')->nullable()->after('lon');
            $table->string('regionCode')->nullable()->after('countryCode');
            $table->string('cityName')->nullable()->after('regionCode');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('location')->nullable()->after('email');

            $table->dropColumn('lat');
            $table->dropColumn('lon');
            $table->dropColumn('countryCode');
            $table->dropColumn('regionCode');
            $table->dropColumn('cityName');
        });
    }
}
