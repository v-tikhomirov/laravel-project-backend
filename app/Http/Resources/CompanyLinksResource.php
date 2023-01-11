<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyLinksResource extends JsonResource
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
            'linkedin' => $this->link_to_linkedin,
            'github' => $this->link_to_github,
            'medium' => $this->link_to_medium,
            'youtube' => $this->link_to_youtube,
            'stackoverflow' => $this->link_to_stackoverflow,
            'facebook' => $this->link_to_facebook,
        ];
    }
}
