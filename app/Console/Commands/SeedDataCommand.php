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

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
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

            $def_temp = rand(22 * 100, 43 * 100) / 100; // min and max of temperature
            $def_humid = rand(48 * 100, 77 * 100) / 100; // min and max of humidity

            // assign the old data to defined variable if it has data
            if ($old_data) {
                $def_temp = rand(($old_data->temperature - 3) * 100, ($old_data->temperature + 3) * 100) / 100;
                $def_humid = rand(($old_data->humidity - 3) * 100, ($old_data->humidity + 3) * 100) / 100;
            }

            // insert the data
            $data = new DHT11Sensor;
            $data->temperature = $def_temp;
            $data->humidity = $def_humid;
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

            $def_co2 = rand(244, 579); // min and max of carbon dioxide
            $def_tvoc = rand(0, 287); // min and max of total volatile organic compound

            // assign the old data to defined variable if it has data
            if ($old_data) {
                $def_co2 = rand($old_data->co2 - 3, $old_data->co2 + 3);
                $def_tvoc = rand($old_data->humidity - 3, $old_data->humidity + 3);
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

            $def_n = rand(0 * 100, 255 * 100) / 100; // min and max of nitrogen
            $def_p = rand(0 * 100, 255 * 100) / 100; // min and max of phosphorus
            $def_k = rand(0 * 100, 255 * 100) / 100; // min and max of potassium

            // assign the old data to defined variable if it has data
            if ($old_data) {
                $def_n = rand(($old_data->nitrogen - 3) * 100, ($old_data->nitrogen + 3) * 100) / 100;
                $def_p = rand(($old_data->phosphorus - 3) * 100, ($old_data->phosphorus + 3) * 100) / 100;
                $def_k = rand(($old_data->potassium - 3) * 100, ($old_data->potassium + 3) * 100) / 100;
            }

            // insert the data
            $data = new NPKSensor;
            $data->nitrogen = $def_n;
            $data->phosphorus = $def_p;
            $data->potassium = $def_k;
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
