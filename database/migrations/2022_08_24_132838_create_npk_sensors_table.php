<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNpkSensorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // The measuring range of the Soil NPK Sensor is 0 to 1999mg/kg, and the working humidity is from 5 to 95%. The maximum power consumption is ≤ 0.15W.
        Schema::create('npk_sensors', function (Blueprint $table) {
            $table->id();
            $table->decimal('nitrogen', 5, 2)->unsigned();
            $table->decimal('phosphorus', 5, 2)->unsigned();
            $table->decimal('potassium', 5, 2)->unsigned();
            $table->integer('batch')->unsigned()->index();
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
        Schema::dropIfExists('soil_npk_sensors');
    }
}
