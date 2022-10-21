<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

// to be moved to Command.php
use Illuminate\Support\Facades\Http;
use App\Models\DHT11Sensor;
use App\Models\SGP30Sensor;
use App\Models\NPKSensor;

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
        $dht11_datas = DHT11Sensor::where('is_uploaded', 0)->get();

        if ($dht11_datas->isNotEmpty()) {
            // $response = Http::post('https://jsonplaceholder.typicode.com/posts', [
            //     'title' => 'foo',
            //     'body' => 'bar',
            //     'userId' => 1
            // ]);
            
            echo 'import here';
        }

        // $response = Artisan::call('command:upload_data_to_web', []);
    
        // if (!$response) {
        //     return $this->sendError('Uploading of data failed.');
        // }
    
        // return $this->sendResponse([], 'Datas uploaded successfully.');
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
