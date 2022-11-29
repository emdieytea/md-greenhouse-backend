<?php

namespace Database\Seeders;

use App\Models\DHT11Sensor;
use App\Models\SGP30Sensor;
use App\Models\NPKSensor;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeviceDataSeeder extends Seeder
{
    private $_date_now;
    private $_start_date;
    // private $_end_date;
    private $_increment;

    public function __construct()
    {
        $this->_date_now  = Carbon::now();
        $this->_start_date = Carbon::parse(env('APP_DATA_START_DATE', '2022-03-01 10:04:01'));
        // $this->_end_date = Carbon::parse(env('APP_DATA_END_DATE', '2022-03-02 10:04:01'));
        $this->_increment = 1.3; // increment value of the min and max value
    }

    /**
     * Generates data for DHT11 Sensor
     * 
     * @return void
     */
    private function GenDHT11Data($date, $batch)
    {
        $the_date = Carbon::parse($date); // the date now

        // checks if it has data for this hour using the date now as its start and end date
        $check = DHT11Sensor::whereDate('created_at', '>=', $the_date->format('Y-m-d'))
            ->whereTime('created_at', '>=', $the_date->format('H:00:00'))
            ->whereDate('created_at', '<=', $the_date->format('Y-m-d'))
            ->whereTime('created_at', '<=', $the_date->format('H:59:59'))
            ->where('batch', $batch)
            ->get();

        $the_date->subHour(); // subtract an hour to get the data an hour ago
            
        if ($check->isEmpty()) {
            // find a data past hour ago
            $old_data = DHT11Sensor::whereDate('created_at', '>=', $the_date->format('Y-m-d'))
                ->whereTime('created_at', '>=', $the_date->format('H:00:00'))
                ->whereDate('created_at', '<=', $the_date->format('Y-m-d'))
                ->whereTime('created_at', '<=', $the_date->format('H:59:59'))
                ->where('batch', $batch)
                ->latest('created_at')
                ->first();

            $temp_min = 20; $temp_max = 43; // min and max of temperature
            $def_temp = rand($temp_min, $temp_max);
            $def_temp_d = rand(0, 99); // min and max decimal of temperature
            $humid_min = 48; $humid_max = 77; // min and max of humidity
            $def_humid = rand($humid_min, $humid_max);
            $def_humid_d = rand(0, 99); // min and max decimal of humidity
            
            // assign the old data to defined variable if it has data
            if ($old_data) {
                $def_temp = rand($old_data->temperature - $this->_increment, $old_data->temperature + $this->_increment);
                $def_humid = rand($old_data->humidity - $this->_increment, $old_data->humidity + $this->_increment);
                
                while ($def_temp < $temp_min || $def_temp > $temp_max)
                    $def_temp = rand($old_data->temperature - $this->_increment, $old_data->temperature + $this->_increment);

                while ($def_humid < $humid_min || $def_humid > $humid_max)
                    $def_humid = rand($old_data->humidity - $this->_increment, $old_data->humidity + $this->_increment);
            }

            // insert the data
            $data = new DHT11Sensor;
            $data->temperature = $def_temp . '.' . $def_temp_d;
            $data->humidity = $def_humid . '.' . $def_humid_d;
            $data->batch = $batch;
            $data->created_at = $date;
            $data->updated_at = null;
            $data->save();

            return true;
        }

        return false;
    }
    
    /**
     * Generates data for SGP30 Sensor
     * 
     * @return void
     */
    private function GenSGP30Data($date, $batch)
    {
        $the_date = Carbon::parse($date); // the date now

        // checks if it has data for this hour using the date now as its start and end date
        $check = SGP30Sensor::whereDate('created_at', '>=', $the_date->format('Y-m-d'))
            ->whereTime('created_at', '>=', $the_date->format('H:00:00'))
            ->whereDate('created_at', '<=', $the_date->format('Y-m-d'))
            ->whereTime('created_at', '<=', $the_date->format('H:59:59'))
            ->where('batch', $batch)
            ->get();

        $the_date->subHour(); // subtract an hour to get the data an hour ago
            
        if ($check->isEmpty()) {
            // find a data past hour ago
            $old_data = SGP30Sensor::whereDate('created_at', '>=', $the_date->format('Y-m-d'))
                ->whereTime('created_at', '>=', $the_date->format('H:00:00'))
                ->whereDate('created_at', '<=', $the_date->format('Y-m-d'))
                ->whereTime('created_at', '<=', $the_date->format('H:59:59'))
                ->where('batch', $batch)
                ->latest('created_at')
                ->first();

            $co2_min = 244; $co2_max = 579; // min and max of carbon dioxide
            $def_co2 = rand($co2_min, $co2_max);
            $tvoc_min = 0; $tvoc_max = 287; // min and max of total volatile organic compound
            $def_tvoc = rand($tvoc_min, $tvoc_max);
            
            // assign the old data to defined variable if it has data
            if ($old_data) {
                $def_co2 = rand($old_data->co2 - $this->_increment, $old_data->co2 + $this->_increment);
                $def_tvoc = rand($old_data->tvoc - $this->_increment, $old_data->tvoc + $this->_increment);
                
                while ($def_co2 < $co2_min || $def_co2 > $co2_max)
                    $def_co2 = rand($old_data->co2 - $this->_increment, $old_data->co2 + $this->_increment);

                while ($def_tvoc < $tvoc_min || $def_tvoc > $tvoc_max)
                    $def_tvoc = rand($old_data->tvoc - $this->_increment, $old_data->tvoc + $this->_increment);
            }

            // insert the data
            $data = new SGP30Sensor;
            $data->co2 = $def_co2;
            $data->tvoc = $def_tvoc;
            $data->batch = $batch;
            $data->created_at = $date;
            $data->updated_at = null;
            $data->save();

            return true;
        }

        return false;
    }
    
    /**
     * Generates data for NPK Sensor
     * 
     * @return void
     */
    private function GenNPKData($date, $batch)
    {
        $the_date = Carbon::parse($date); // the date now

        // checks if it has data for this hour using the date now as its start and end date
        $check = NPKSensor::whereDate('created_at', '>=', $the_date->format('Y-m-d'))
            ->whereTime('created_at', '>=', $the_date->format('H:00:00'))
            ->whereDate('created_at', '<=', $the_date->format('Y-m-d'))
            ->whereTime('created_at', '<=', $the_date->format('H:59:59'))
            ->where('batch', $batch)
            ->get();

        $the_date->subHour(); // subtract an hour to get the data an hour ago
            
        if ($check->isEmpty()) {
            // find a data past hour ago
            $old_data = NPKSensor::whereDate('created_at', '>=', $the_date->format('Y-m-d'))
                ->whereTime('created_at', '>=', $the_date->format('H:00:00'))
                ->whereDate('created_at', '<=', $the_date->format('Y-m-d'))
                ->whereTime('created_at', '<=', $the_date->format('H:59:59'))
                ->where('batch', $batch)
                ->latest('created_at')
                ->first();
            
            $n_min = 0; $n_max = 255; // min and max of nitrogen
            $def_n = rand($n_min, $n_max);
            $def_n_d = rand(0, 99); // min and max decimal of nitrogen
            $p_min = 0; $p_max = 255; // min and max of phosphorus
            $def_p = rand($p_min, $p_max);
            $def_p_d = rand(0, 99); // min and max decimal of phosphorus
            $k_min = 0; $k_max = 255; // min and max of potassium
            $def_k = rand($k_min, $k_max);
            $def_k_d = rand(0, 99); // min and max decimal of potassium
            
            // assign the old data to defined variable if it has data
            if ($old_data) {
                $def_n = rand($old_data->nitrogen - $this->_increment, $old_data->nitrogen + $this->_increment);
                $def_p = rand($old_data->phosphorus - $this->_increment, $old_data->phosphorus + $this->_increment);
                $def_k = rand($old_data->potassium - $this->_increment, $old_data->potassium + $this->_increment);

                while ($def_n < $n_min || $def_n > $n_max)
                    $def_n = rand($old_data->nitrogen - $this->_increment, $old_data->nitrogen + $this->_increment);

                while ($def_p < $p_min || $def_p > $p_max)
                    $def_p = rand($old_data->phosphorus - $this->_increment, $old_data->phosphorus + $this->_increment);
                    
                while ($def_k < $k_min || $def_k > $k_max)
                    $def_k = rand($old_data->potassium - $this->_increment, $old_data->potassium + $this->_increment);
            }

            // insert the data
            $data = new NPKSensor;
            $data->nitrogen = $def_n . '.' . $def_n_d;
            $data->phosphorus = $def_p . '.' . $def_p_d;
            $data->potassium = $def_k . '.' . $def_k_d;
            $data->batch = $batch;
            $data->created_at = $date;
            $data->updated_at = null;
            $data->save();

            return true;
        }

        return false;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // counters
        $dht11_counter = 0;
        $sgp30_counter = 0;
        $npk_counter = 0;

        while ($this->_start_date->timestamp < $this->_date_now->timestamp) {
            $batches = explode(',', env('APP_ACTIVE_BATCHES')); // get the batches from env and convert them to array
            shuffle($batches); // shuffle the array batch

            // loop through the batches if it has array value
            foreach ($batches as $batch) {
                $dht11 = $this->GenDHT11Data($this->_start_date, $batch);
                if ($dht11) $dht11_counter++;
                $sgp30 = $this->GenSGP30Data($this->_start_date, $batch);
                if ($sgp30) $sgp30_counter++;
                $npk = $this->GenNPKData($this->_start_date, $batch);
                if ($npk) $npk_counter++;
            }

            $this->_start_date->addHour();
        }
        
        $elapsed_time = Carbon::now()->diffInSeconds($this->_date_now);

        // format the numbers to add thousand separator (,)
        $dht11_counter = number_format($dht11_counter);
        $sgp30_counter = number_format($sgp30_counter);
        $npk_counter = number_format($npk_counter);

        echo "$dht11_counter records are inserted for dht11_sensors table.\n$sgp30_counter records are inserted for sgp30_sensors table.\n$npk_counter records are inserted for npk_sensors table.\nElapsed Time: " . intval($elapsed_time / 60) . " minute(s) " . ($elapsed_time % 60) . " second(s)\n";
    }
}
