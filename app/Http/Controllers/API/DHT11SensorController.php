<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
// use App\Http\Resources\DHT11SensorResource;
use App\Models\DHT11Sensor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DHT11SensorController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $batches = DHT11Sensor::select(['batch'])->groupBy('batch')->orderBy('batch', 'asc')->pluck('batch')->toArray();
        
        if (count($batches) <= 0) {
            return $this->sendError('Error.', 'No Data Found.', 404);
        }
        
        $labelDates = DHT11Sensor::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00') AS created_at")
            )
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00')"))
            ->orderBy('created_at', 'asc')
            ->pluck('created_at')
            ->toArray();

        $arr = [];

        $arr['labels'] = $labelDates;

        foreach ($batches as $batch) {
            $arr['batch' . $batch] = [];
            
            $datas = DHT11Sensor::where('batch', $batch)->orderBy('created_at', 'asc')->get();
            
            foreach ($labelDates as $date) {
                foreach ($datas as $i => $data) {
                    $isFound = false;
                    if ($date->format('Y-m-d H:00:00') == $data->created_at->format('Y-m-d H:00:00')) {
                        $arr['batch' . $batch][0][] = $data->temperature;
                        $arr['batch' . $batch][1][] = $data->humidity;
                        $i++;
                        $isFound = true;
                        break;
                    }
                }

                if (!$isFound) {
                    $arr['batch' . $batch][0][] = 0;
                    $arr['batch' . $batch][1][] = 0;
                }
            }

        }

        ///////////////
        // $labelDates = [];

        // $start_date = Carbon::parse('2022-03-01 00:00:00');

        // $i = 0;
        // while ($start_date < Carbon::now()) {
        //     echo $start_date->timezone(env('APP_TIMEZONE', 'Asia/Manila'))->format('Y-m-d H:00:00') . "\n";
        //     $i++;
        //     $start_date->addHour();
        // }
        ///////////////

        // return $this->sendResponse(DHT11SensorResource::collection($datas), 'Datas retrieved successfully.');
        return $this->sendResponse($arr, 'Datas retrieved successfully.');
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }
   
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
