<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
// use App\Http\Resources\DHT11SensorResource;
use App\Models\DHT11Sensor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DHT11SensorController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $inputs = $request->only('start_date', 'end_date');

            if ((Str::of($inputs['start_date'])->isEmpty() && Str::of($inputs['end_date'])->isNotEmpty()) || (Str::of($inputs['start_date'])->isNotEmpty() && Str::of($inputs['end_date'])->isEmpty())) {
                return $this->sendError('Error.', 'Start and End date should be empty or has both value.', 400);
            }

            if (Str::of($inputs['start_date'])->isEmpty()) {
                $inputs['start_date'] = Carbon::now()->startOfMonth();
            } else {
                $inputs['start_date'] = Carbon::parse($inputs['start_date']);
            }

            if (Str::of($inputs['end_date'])->isEmpty()) {
                $inputs['end_date'] = Carbon::now()->endOfMonth();
            } else {
                $inputs['end_date'] = Carbon::parse($inputs['end_date']);
            }

            $batches = DHT11Sensor::select(['batch'])->groupBy('batch')->orderBy('batch', 'asc')->pluck('batch')->toArray();
            
            // if (count($batches) <= 0) {
            //     return $this->sendError('Error.', 'No Data Found.', 404);
            // }
            
            // $labelDates = DHT11Sensor::select(
            //         DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00') AS created_at")
            //     )
            //     ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00')"))
            //     ->orderBy('created_at', 'asc')
            //     ->pluck('created_at')
            //     ->toArray();

            $labelDates = [];

            $start_date = Carbon::parse($inputs['start_date']->format('Y-m-d') . ' 00:00:00');
            $end_date = Carbon::parse($inputs['end_date']->format('Y-m-d') . ' 23:00:00');
            
            while ($start_date <= $end_date) {
                $labelDates[] = $start_date->format('Y-m-d H:00');
                $start_date->addHour();
            }

            $arr = [];

            $arr['labels'] = $labelDates;
    
            foreach ($batches as $batch) {
                $arr['batch' . $batch] = [];
                
                $datas = DHT11Sensor::where('batch', $batch)->orderBy('created_at', 'asc')->get();
                
                foreach ($labelDates as $date) {
                    foreach ($datas as $i => $data) {
                        $isFound = false;
                        if (Carbon::parse($date)->format('Y-m-d H:00:00') == $data->created_at->format('Y-m-d H:00:00')) {
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

            // return $this->sendResponse(DHT11SensorResource::collection($datas), 'Datas retrieved successfully.');
            return $this->sendResponse($arr, 'Datas retrieved successfully.');
        } catch (\Carbon\Exceptions\InvalidFormatException $e) {
            return $this->sendError('Error.', 'Invalid Date Format.', 400); // $e->getMessage()
        }
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
