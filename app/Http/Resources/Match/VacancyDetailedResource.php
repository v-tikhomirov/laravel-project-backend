<?php

namespace App\Http\Resources\Match;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\DataPreparation;

class VacancyDetailedResource extends JsonResource
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
            'company_id' => $this->company_id,
            'status' => $this->status,
            'position' => $this->position,
            'industry' => $this->getIndustryText(),
            'office_type' => $this->office_type,
            'is_ready_to_relocate' => $this->is_ready_to_relocate,
            'relocation_benefits' => $this->relocation_benefits,
            'location' => [
                'city' => $this->city_id,
                'country' => $this->country_id
            ],
            'currency' => $this->currency,
            'desired_salary' => $this->desired_salary,
            'max_salary' => $this->max_salary,
            'description' => $this->description,
            'benefits' => $this->getBenefitsText(),
            'languages' => $this->getLanguagesArray(),
            'skills' => $this->getDetailedSkills(),
            'about' => $this->about
        ];
    }
}
