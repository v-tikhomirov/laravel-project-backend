<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Nnjeim\World\World;

class CandidateResource extends JsonResource
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
        return [
            'first_name' => $this->profile->first_name,
            'last_name' => $this->profile->last_name,
            'country_id' => $this->profile->country_id,
            'city_id' => $this->profile->city_id,
            'country' => $country['name'] ?? '',
            'city' => $city['name'] ?? '',
            'profile_picture' => $this->profile->profile_picture
        ];
    }
}
