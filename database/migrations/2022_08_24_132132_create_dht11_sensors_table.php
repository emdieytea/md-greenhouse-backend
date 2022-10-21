<?php 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDht11SensorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dht11_sensors', function (Blueprint $table) {
            $table->id();
            $table->decimal('temperature', 5, 2)->unsigned(); // temperature range is from 0 to 50 degrees Celsius with +-2 degrees accuracy
            $table->decimal('humidity', 5, 2)->unsigned(); // humidity range is from 20 to 80% with 5% accuracy
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
        Schema::dropIfExists('dht11_sensors');
    }
}
