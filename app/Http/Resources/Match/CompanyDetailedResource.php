<?php

namespace App\Http\Resources\Match;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\DataPreparation;

class CompanyDetailedResource extends JsonResource
{
    use DataPreparation;
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
            'name' => $this->name,
            'type' => $this->getTypeArray(),
            'website' => $this->website,
            'number_of_employees' => $this->number_of_employees,
            'location' => [
                'city' => $this->city_id,
                'country' => $this->country_id
            ],
            'about' => $this->about,
            'benefits' => $this->getBenefitsText(),
        ];
    }

    protected function getTypeArray(): array
    {
        return explode(';', $this->type);
    }
}
