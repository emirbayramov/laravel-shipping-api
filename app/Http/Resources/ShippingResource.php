<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShippingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'description'=>$this->description,
            'origin'=>$this->origin,
            'destination'=>$this->destination,
            'weight' => $this->weight,
            'distance'=> $this->distance,
            'price' => $this->price
        ];
    }
}
