<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Nnjeim\World\World;

class MatchCardResource extends JsonResource
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
                'id' => $this->vacancy->country_id,
            ]
        ]);
        if ($action->success) {
            $country = $action->data->first();
        }
        $action =  World::cities([
            'filters' => [
                'id' => $this->vacancy->city_id,
            ]
        ]);
        if ($action->success) {
            $city = $action->data->first();
        }
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'company_id' => $this->company_id,
            'cv_id' => $this->cv_id,
            'vacancy_id' => $this->vacancy_id,
            'status' => $this->status,
            'percent' => $this->percent,
            'decline_reason' => $this->decline_reason,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'cv' => [
                'id' => $this->cv->id,
                'type' => $this->cv->type,
                'office_type' => $this->cv->office_type,
                'is_ready_to_relocate' => $this->cv->is_ready_to_relocate,
                'minimal_salary' => $this->cv->minimal_salary,
                'desired_salary' => $this->cv->desired_salary,
                'currency' => $this->cv->currency,
                'status' => $this->cv->status,
                'position' => $this->cv->position,
                'about' => $this->cv->about,
                'skills' => $this->getSkills($this->cv->skills),
                'experience' => $this->getExperience()
            ],
            'vacancy' => [
                'id' =>$this->vacancy->id,
                'position' =>$this->vacancy->position,
                'currency' =>$this->vacancy->currency,
                'desired_salary' =>$this->vacancy->desired_salary,
                'max_salary' =>$this->vacancy->max_salary,
                'office_type' =>$this->vacancy->office_type,
                'is_ready_to_relocate' =>$this->vacancy->is_ready_to_relocate,
                'country_id' =>$this->vacancy->country_id,
                'city_id' =>$this->vacancy->city_id,
                'country' => $country['name'] ?? '',
                'city' => $city['name'] ?? '',
                'description' =>$this->vacancy->description,
                'about' =>$this->vacancy->about,
                'skills' => $this->getSkills($this->vacancy->skills)
            ],
            'company' => CompanyFullResource::make($this->company)->resolve(),
            'candidate' => CandidateResource::make($this->candidate)->resolve()
        ];
    }

    protected function getSkills($skills): array
    {
        $out = [];
        $tmp = [];
        $extra = [];
        foreach ($skills as $skill) {
            $tmp[] = [
                'name' => $skill->technology->name,
                'type' => $skill->technology->type
            ];
            if (isset($extra[$skill->technology->type])) {
                $tmpName = $extra[$skill->technology->type]['label'] . ', ' .$skill->technology->name;
                if (strlen($tmpName) <= 26) {
                    $extra[$skill->technology->type]['label'] = $extra[$skill->technology->type]['label'] . ', ' .$skill->technology->name;
                } else {
                    $extra[$skill->technology->type]['count'] += 1;
                }
            } else {
                $extra[$skill->technology->type] = [
                    'label' => $skill->technology->name,
                    'count' => 0
                ];
            }
        }
        $grouped = collect($tmp)->groupBy('type')->toArray();
        foreach ($grouped as $key => $items) {
            $out[$key] = [
                'data' => $items,
                'label' => $extra[$key]['label'],
                'count' => $extra[$key]['count']
            ];
        }
        return array_slice($out, 0, 2);
    }

    protected function getExperience()
    {
        $out = 0;
        foreach ($this->cv->recentProjects as $project) {
            $out+= $project->getExperience();
        }

        return $out;
    }
}
