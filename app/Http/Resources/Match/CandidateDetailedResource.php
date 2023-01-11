<?php

namespace App\Http\Resources\Match;

use Illuminate\Http\Resources\Json\JsonResource;
use Nnjeim\World\World;

class CandidateDetailedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $action =  World::countries([
            'filters' => [
                'id' => $this->profile->country_id,
            ]
        ]);
        if ($action->success) {
            $country = $action->data->first();
        }
        $action =  World::cities([
            'filters' => [
                'id' => $this->profile->city_id,
            ]
        ]);
        if ($action->success) {
            $city = $action->data->first();
        }
        //@todo remove contacts if match not in correct status
        return [
            'first_name' => $this->profile->first_name,
            'last_name' => $this->profile->last_name,
            'country_id' => $this->profile->country_id,
            'city_id' => $this->profile->city_id,
            'country' => $country['name'] ?? '',
            'city' => $city['name'] ?? '',
            'whatsapp' => $this->profile->whatsapp,
            'telegram' => $this->profile->telegram,
            'email' => $this->email,
            'phone' => $this->profile->country_code . $this->profile->phone,
            'link_to_facebook' => $this->profile->link_to_facebook,
            'link_to_github' => $this->profile->link_to_github,
            'link_to_linkedin' => $this->profile->link_to_linkedin,
            'link_to_medium' => $this->profile->link_to_medium,
            'link_to_other' => $this->profile->link_to_other,
            'link_to_stackoverflow' => $this->profile->link_to_stackoverflow,
            'link_to_youtube' => $this->profile->link_to_youtube,
            'profile_picture' => $this->profile->profile_picture
        ];
    }
}
