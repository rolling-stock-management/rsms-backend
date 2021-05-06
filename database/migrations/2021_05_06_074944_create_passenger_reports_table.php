<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePassengerReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('passenger_reports', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->date('date');
            $table->text('problem_description');
            $table->integer('wagon_number');
            $table->foreignId('train_id')->constrained('trains')->onDelete('cascade');
            $table->foreignId('wagon_id')->nullable()->constrained('passenger_wagons')->onDelete('cascade');
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
        Schema::dropIfExists('passenger_reports');
    }
}
