<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NPKSensorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        
        return [
            'id' => $this->id,
            'nitrogen' => $this->nitrogen,
            'phosphorus' => $this->phosphorus,
            'potassium' => $this->potassium,
            'batch' => $this->batch,
            'created_at' => $this->created_at->timezone(env('APP_TIMEZONE'))->format('Y-m-d H:00:00'),
            // 'updated_at' => $this->updated_at->timezone(env('APP_TIMEZONE'))->format('Y-m-d H:i:s'),
        ];
    }
}
