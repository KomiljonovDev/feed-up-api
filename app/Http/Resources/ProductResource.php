<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'image'=>$this->image,
            'created_at' => $this->created_at->format('d-m-Y H:i:s'),
            'category' => new CategoryResource($this->whenLoaded('category'))
        ];
    }
}
