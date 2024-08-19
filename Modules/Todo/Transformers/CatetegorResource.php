<?php

namespace Modules\Todo\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class CatetegorResource extends JsonResource
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
            'name' => $this->name,
            'category_id' => $this->category_id,
            'workspace_id' => $this->workspace_id,
            'user_id' => $this->user_id,
        ];  
    }
}
