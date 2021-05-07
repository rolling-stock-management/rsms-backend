<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageFileNameColumnToPassengerReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('passenger_reports', function (Blueprint $table) {
            $table->string('image_file_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('passenger_reports', function (Blueprint $table) {
            $table->dropColumn('image_file_name');
        });
    }
}
