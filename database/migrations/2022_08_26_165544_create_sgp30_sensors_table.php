<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSgp30SensorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sgp30_sensors', function (Blueprint $table) {
            $table->id();
            $table->integer('co2')->unsigned();     // ppm
            $table->integer('tvoc')->unsigned();    // ppb
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
        Schema::dropIfExists('sgp30_air_quality_sensors');
    }
}
