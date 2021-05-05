<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRollingStockTrainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rolling_stock_trains', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->integer('position');
            $table->string('comment')->nullable();
            $table->foreignId('train_id')->constrained('trains')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('trainable_id')->nullable();
            $table->string('trainable_type')->nullable();
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
        Schema::dropIfExists('rolling_stock_trains');
    }
}
