<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePassengerWagonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('passenger_wagons', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('type_id')->constrained('passenger_wagon_types')->onDelete('cascade');
            $table->string('letter_marking')->nullable();
            $table->integer('tare')->nullable();
            $table->integer('total_weight')->nullable();
            $table->integer('seats_count')->nullable();
            $table->integer('max_speed')->nullable();
            $table->decimal('length')->nullable();
            $table->string('brake_marking')->nullable();
            $table->foreignId('owner_id')->nullable()->constrained('owners')->onDelete('set null');
            $table->foreignId('status_id')->nullable()->constrained('statuses')->onDelete('set null');
            $table->date('repair_date')->nullable();
            $table->date('repair_valid_until')->nullable();
            $table->foreignId('repair_workshop_id')->nullable()->constrained('repair_workshops')->onDelete('set null');
            $table->foreignId('depot_id')->nullable()->constrained('depots')->onDelete('set null');
            $table->text('other_info')->nullable();
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
        Schema::dropIfExists('passenger_wagons');
    }
}
