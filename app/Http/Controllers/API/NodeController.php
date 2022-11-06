<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\NodeResource;
use App\Models\Node;
use Illuminate\Http\Request;
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
            'name' => 'required|string',
            // 'description' => 'required',
            'url' => 'required|url'
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        $data = new Node;
        $data->name = $input['name'];
        $data->description = $input['description'];
        $data->url = $input['url'];
        $data->save();
    
        return $this->sendResponse(new NodeResource($data), 'Data created successfully.');
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
            'name' => 'required|string',
            // 'description' => 'required',
            'url' => 'required|url'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $data->name = $input['name'];
        $data->description = $input['description'];
        $data->url = $input['url'];
        $data->save();
   
        return $this->sendResponse(new NodeResource($data), 'Data updated successfully.');
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
}
