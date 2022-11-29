<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\NodeResource;
use App\Models\Node;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class NodeController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datas = Node::all();
    
        return $this->sendResponse(NodeResource::collection($datas), 'Datas retrieved successfully.');
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
        
        $validator = Validator::make($input, [
            'batch_no' => 'required|not_in:0|numeric', // unique:nodes
            'name' => 'required|string',
            // 'description' => 'required',
            'status' => 'required',
            'url' => 'nullable|url'
        ], [
            'batch_no.not_in' => "The :attribute should not be equal to 0.",
        ], [
            'batch_no' => 'batch #',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $isNew = false;
        $data = Node::where('batch_no', $input['batch_no'])->first();

        if (!$data) {
            $data = new Node;
            $isNew = true;
        }
        
        $data->batch_no = $input['batch_no'];
        $data->name = $input['name'];

        if ($request->filled('description'))
            $data->description = $input['description'];

        $data->status = $input['status'];
        
        if ($request->filled('url'))
            $data->url = $input['url'];

        $data->updated_at = Carbon::now();
        $data->save();
    
        return $this->sendResponse(new NodeResource($data), 'Data ' . ($isNew ? 'created' : 'updated') . ' successfully.');
    }
   
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Node::find($id);
  
        if (is_null($data)) {
            return $this->sendError('Data not found.');
        }
   
        return $this->sendResponse(new NodeResource($data), 'Data retrieved successfully.');
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
        $data = Node::find($id);
        
        if (is_null($data)) {
            return $this->sendError('Data not found.');
        }

        $input = $request->all();
   
        $validator = Validator::make($input, [
            'url' => 'nullable|url' // required
        ]);
   
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $data->url = $input['url'];
        $data->save();
   
        return $this->sendResponse(new NodeResource($data), 'Data URL updated successfully.');
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Node::find($id);
        
        if (is_null($data)) {
            return $this->sendError('Data not found.');
        }
   
        $data->delete();
   
        return $this->sendResponse([], 'Data deleted successfully.');
    }

    /**
     * Refreshes the data of the backend from the nodes immediately.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function refresh_data()
    {
        try {
            // will get nodes that are not null and empty
            $nodes = Node::whereNotNull('url')->where('url', '!=', '')->get();

            $arr = [];

            if ($nodes->isNotEmpty()) {
                foreach ($nodes as $node) {
                    $response = Http::withHeaders([
                        'App-Auth-Key' => 'app-auth-key',
                    ])->get($node->url . '/api/v1/upload-data');

                    // $response->body() : string;
                    // $response->json() : array|mixed;
                    // $response->object() : object;
                    // $response->collect() : Illuminate\Support\Collection;
                    // $response->status() : int;
                    // $response->ok() : bool;
                    // $response->successful() : bool;
                    // $response->failed() : bool;
                    // $response->serverError() : bool;
                    // $response->clientError() : bool;
                    // $response->header($header) : string;
                    // $response->headers() : array;

                    if ($response->status() == 200) {
                        $arr[] = $node->name . "'s data has been uploaded successfully.";
                    } else {
                        $arr[] = $node->name . "'s data failed to uploaded.";
                    }
                }
            }

            return $this->sendResponse($arr, 'Data refresh executed successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Error.', 'Something went wrong, please try again.', 400); // $e->getMessage()
        }
    }
}
