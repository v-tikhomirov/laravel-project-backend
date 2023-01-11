<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\DataPreparation;

class CvFullResource extends JsonResource
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
            'is_draft' => $this->is_draft,
            'primary' => [
                'position' => $this->position,
                'industry' => $this->getIndustryArray(),
                'type' => $this->type,
                'office_type' => $this->office_type,
                'status' => $this->status,
                'is_ready_to_relocate' => $this->is_ready_to_relocate,
                'languages' => $this->getLanguagesArray(),
                'currency' => $this->currency,
                'desired_salary' => $this->desired_salary,
                'minimal_salary' => $this->minimal_salary,
                'about' => $this->about,
            ],
            'skills' => $this->getSkillsArray(),
            'recent_projects' => $this->getRecentProjectsArray()
        ];
    }
}
