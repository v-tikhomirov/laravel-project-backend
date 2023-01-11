<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SkillsResource extends JsonResource
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
            'technology_id' => $this->technology_id,
            'group' => $this->technology->group,
            'name' => $this->technology->name,
            'type' => $this->technology->type,
            'experience' => $this->experience
        ];
    }
}
