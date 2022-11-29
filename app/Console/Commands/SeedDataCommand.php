<?php

namespace App\Console\Commands;

use App\Models\DHT11Sensor;
use App\Models\SGP30Sensor;
use App\Models\NPKSensor;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SeedDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:seed_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seeds data for the batches.';

    private $_increment_dht11;
    private $_increment_sgp30;
    private $_increment_npk;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->_increment_dht11 = 1.3; // increment value of the min and max value of dht11
        $this->_increment_sgp30 = 2; // increment value of the min and max value of sgp30
        $this->_increment_npk = 3; // increment value of the min and max value of npk
    }

    /**
     * Generates data for DHT11 Sensor
     * 
     * @return void
     */
    private function GenDHT11Data($batch)
    {
        $date_now = Carbon::now(); // the date now

        // checks if it has data for this hour using the date now as its start and end date
        $check = DHT11Sensor::whereDate('created_at', '>=', $date_now->format('Y-m-d'))
            ->whereTime('created_at', '>=', $date_now->format('H:00:00'))
            ->whereDate('created_at', '<=', $date_now->format('Y-m-d'))
            ->whereTime('created_at', '<=', $date_now->format('H:59:59'))
            ->where('batch', $batch)
            ->get();

        $date_now->subHour(); // subtract an hour to get the data an hour ago
            
        if ($check->isEmpty()) {
            // find a data past hour ago
            $old_data = DHT11Sensor::whereDate('created_at', '>=', $date_now->format('Y-m-d'))
                ->whereTime('created_at', '>=', $date_now->format('H:00:00'))
                ->whereDate('created_at', '<=', $date_now->format('Y-m-d'))
                ->whereTime('created_at', '<=', $date_now->format('H:59:59'))
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
                $def_temp = rand($old_data->temperature - $this->_increment_dht11, $old_data->temperature + $this->_increment_dht11);
                $def_humid = rand($old_data->humidity - $this->_increment_dht11, $old_data->humidity + $this->_increment_dht11);
                
                while ($def_temp < $temp_min || $def_temp > $temp_max)
                    $def_temp = rand($old_data->temperature - $this->_increment_dht11, $old_data->temperature + $this->_increment_dht11);

                while ($def_humid < $humid_min || $def_humid > $humid_max)
                    $def_humid = rand($old_data->humidity - $this->_increment_dht11, $old_data->humidity + $this->_increment_dht11);
            }

            // insert the data
            $data = new DHT11Sensor;
            $data->temperature = $def_temp . '.' . $def_temp_d;
            $data->humidity = $def_humid . '.' . $def_humid_d;
            $data->batch = $batch;
            $data->updated_at = null;
            $data->save();
        }
    }
    
    /**
     * Generates data for SGP30 Sensor
     * 
     * @return void
     */
    private function GenSGP30Data($batch)
    {
        $date_now = Carbon::now(); // the date now

        // checks if it has data for this hour using the date now as its start and end date
        $check = SGP30Sensor::whereDate('created_at', '>=', $date_now->format('Y-m-d'))
            ->whereTime('created_at', '>=', $date_now->format('H:00:00'))
            ->whereDate('created_at', '<=', $date_now->format('Y-m-d'))
            ->whereTime('created_at', '<=', $date_now->format('H:59:59'))
            ->where('batch', $batch)
            ->get();

        $date_now->subHour(); // subtract an hour to get the data an hour ago
            
        if ($check->isEmpty()) {
            // find a data past hour ago
            $old_data = SGP30Sensor::whereDate('created_at', '>=', $date_now->format('Y-m-d'))
                ->whereTime('created_at', '>=', $date_now->format('H:00:00'))
                ->whereDate('created_at', '<=', $date_now->format('Y-m-d'))
                ->whereTime('created_at', '<=', $date_now->format('H:59:59'))
                ->where('batch', $batch)
                ->latest('created_at')
                ->first();
            
            $co2_min = 244; $co2_max = 579; // min and max of carbon dioxide
            $def_co2 = rand($co2_min, $co2_max);
            $tvoc_min = 0; $tvoc_max = 287; // min and max of total volatile organic compound
            $def_tvoc = rand($tvoc_min, $tvoc_max);
            
            // assign the old data to defined variable if it has data
            if ($old_data) {
                $def_co2 = rand($old_data->co2 - $this->_increment_sgp30, $old_data->co2 + $this->_increment_sgp30);
                $def_tvoc = rand($old_data->tvoc - $this->_increment_sgp30, $old_data->tvoc + $this->_increment_sgp30);
                
                while ($def_co2 < $co2_min || $def_co2 > $co2_max)
                    $def_co2 = rand($old_data->co2 - $this->_increment_sgp30, $old_data->co2 + $this->_increment_sgp30);

                while ($def_tvoc < $tvoc_min || $def_tvoc > $tvoc_max)
                    $def_tvoc = rand($old_data->tvoc - $this->_increment_sgp30, $old_data->tvoc + $this->_increment_sgp30);
            }

            // insert the data
            $data = new SGP30Sensor;
            $data->co2 = $def_co2;
            $data->tvoc = $def_tvoc;
            $data->batch = $batch;
            $data->updated_at = null;
            $data->save();
        }
    }
    
    /**
     * Generates data for NPK Sensor
     * 
     * @return void
     */
    private function GenNPKData($batch)
    {
        $date_now = Carbon::now(); // the date now

        // checks if it has data for this hour using the date now as its start and end date
        $check = NPKSensor::whereDate('created_at', '>=', $date_now->format('Y-m-d'))
            ->whereTime('created_at', '>=', $date_now->format('H:00:00'))
            ->whereDate('created_at', '<=', $date_now->format('Y-m-d'))
            ->whereTime('created_at', '<=', $date_now->format('H:59:59'))
            ->where('batch', $batch)
            ->get();

        $date_now->subHour(); // subtract an hour to get the data an hour ago
            
        if ($check->isEmpty()) {
            // find a data past hour ago
            $old_data = NPKSensor::whereDate('created_at', '>=', $date_now->format('Y-m-d'))
                ->whereTime('created_at', '>=', $date_now->format('H:00:00'))
                ->whereDate('created_at', '<=', $date_now->format('Y-m-d'))
                ->whereTime('created_at', '<=', $date_now->format('H:59:59'))
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
                $def_n = rand($old_data->nitrogen - $this->_increment_npk, $old_data->nitrogen + $this->_increment_npk);
                $def_p = rand($old_data->phosphorus - $this->_increment_npk, $old_data->phosphorus + $this->_increment_npk);
                $def_k = rand($old_data->potassium - $this->_increment_npk, $old_data->potassium + $this->_increment_npk);

                while ($def_n < $n_min || $def_n > $n_max)
                    $def_n = rand($old_data->nitrogen - $this->_increment_npk, $old_data->nitrogen + $this->_increment_npk);

                while ($def_p < $p_min || $def_p > $p_max)
                    $def_p = rand($old_data->phosphorus - $this->_increment_npk, $old_data->phosphorus + $this->_increment_npk);
                    
                while ($def_k < $k_min || $def_k > $k_max)
                    $def_k = rand($old_data->potassium - $this->_increment_npk, $old_data->potassium + $this->_increment_npk);
            }

            // insert the data
            $data = new NPKSensor;
            $data->nitrogen = $def_n . '.' . $def_n_d;
            $data->phosphorus = $def_p . '.' . $def_p_d;
            $data->potassium = $def_k . '.' . $def_k_d;
            $data->batch = $batch;
            $data->updated_at = null;
            $data->save();
        }
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('[' . Carbon::now()->format('Y-m-d H:i:s') . '] Executing seeding of data to batches!');

        try {
            DB::beginTransaction();
            
            $batches = explode(',', env('APP_ACTIVE_BATCHES')); // get the batches from env and convert them to array
            shuffle($batches); // shuffle the array batch

            // loop through the batches if it has array value
            foreach ($batches as $batch) {
                $this->GenDHT11Data($batch);
                $this->GenSGP30Data($batch);
                $this->GenNPKData($batch);
            }
            
            DB::commit();

            $this->info('[' . Carbon::now()->format('Y-m-d H:i:s') . '] Seeding of data to batches completed!');
            return 200;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('[' . Carbon::now()->format('Y-m-d H:i:s') . '] Something went wrong please try again!'); // $e->getMessage()
            return 400;
        }
    }
}
