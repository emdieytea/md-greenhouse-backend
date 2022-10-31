<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\DHT11Sensor;
use App\Models\SGP30Sensor;
use App\Models\NPKSensor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UploadDataController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $input = $request->all();
        $dht11_datas = $input['dht11_datas'] ?? []; // datas of dht11 sensor
        $npk_datas = $input['npk_datas'] ?? []; // datas of npk sensor
        $sgp30_datas = $input['sgp30_datas'] ?? []; // datas of sgp30 sensor
        
        try {
            DB::beginTransaction();
            if (count($dht11_datas)) {
                $validator = Validator::make($dht11_datas, [
                    '*.temperature' => 'required|numeric',
                    '*.humidity' => 'required|numeric',
                    '*.batch' => 'required|numeric',
                    '*.created_at' => 'required|date_format:Y-m-d\TH:i:s.u\Z',
                ]);

                if ($validator->fails()) {
                    return $this->sendError('Validation Error.', $validator->errors());
                }
                
                foreach($dht11_datas as $data) {
                    // insert data here
                    $dht11 = new DHT11Sensor;
                    $dht11->temperature = $data['temperature'];
                    $dht11->humidity = $data['humidity'];
                    $dht11->batch = $data['batch'];
                    $dht11->created_at = $data['created_at'];
                    $dht11->updated_at = null;
                    $dht11->save();
                }
            }
            
            if (count($npk_datas)) {
                $validator = Validator::make($npk_datas, [
                    '*.nitrogen' => 'required|numeric',
                    '*.phosphorus' => 'required|numeric',
                    '*.potassium' => 'required|numeric',
                    '*.batch' => 'required|numeric',
                    '*.created_at' => 'required|date_format:Y-m-d\TH:i:s.u\Z',
                ]);

                if ($validator->fails()) {
                    return $this->sendError('Validation Error.', $validator->errors());
                }
                
                foreach($npk_datas as $data) {
                    // insert data here
                    $npk = new NPKSensor;
                    $npk->nitrogen = $data['nitrogen'];
                    $npk->phosphorus = $data['phosphorus'];
                    $npk->potassium = $data['potassium'];
                    $npk->batch = $data['batch'];
                    $npk->created_at = $data['created_at'];
                    $npk->updated_at = null;
                    $npk->save();
                }
            }
            
            if (count($sgp30_datas)) {
                $validator = Validator::make($sgp30_datas, [
                    '*.co2' => 'required|numeric',
                    '*.tvoc' => 'required|numeric',
                    '*.batch' => 'required|numeric',
                    '*.created_at' => 'required|date_format:Y-m-d\TH:i:s.u\Z',
                ]);

                if ($validator->fails()) {
                    return $this->sendError('Validation Error.', $validator->errors());
                }
                
                foreach($sgp30_datas as $data) {
                    // insert data here
                    $sgp30 = new SGP30Sensor;
                    $sgp30->co2 = $data['co2'];
                    $sgp30->tvoc = $data['tvoc'];
                    $sgp30->batch = $data['batch'];
                    $sgp30->created_at = $data['created_at'];
                    $sgp30->updated_at = null;
                    $sgp30->save();
                }
            }
            DB::commit();

            return $this->sendResponse([], 'Datas uploaded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Something went wrong please try again!', [], 400); // $e->getMessage()
        }
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
