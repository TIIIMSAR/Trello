<?php

namespace Modules\Todo\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'CategoryName' => $this->name,
            'tasks' => TaskResource::collection($this->tasks),
        ];
    }
}
