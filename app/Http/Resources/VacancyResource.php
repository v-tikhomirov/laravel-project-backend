<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Nnjeim\World\Models\City;
use Nnjeim\World\Models\Country;
use Nnjeim\World\World;

class VacancyResource extends JsonResource
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
                'id' => $this->country_id,
            ]
        ]);
        if ($action->success) {
            $country = $action->data->first();
        }
        $action =  World::cities([
            'filters' => [
                'id' => $this->city_id,
            ]
        ]);
        if ($action->success) {
            $city = $action->data->first();
        }
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'city_id' => $this->city_id,
            'city' => $city['name'] ?? '',
            'country_id' => $this->country_id,
            'country' => $country['name'] ?? '',
            'status' => $this->status,
            'created_at' => $this->created_at,
            'currency' => $this->currency,
            'description' => $this->description,
            'desired_salary' => $this->desired_salary,
            'is_ready_to_relocate' => $this->is_ready_to_relocate,
            'max_salary' => $this->max_salary,
            'office_type' => $this->office_type,
            'position' => $this->position,
            'relocation_benefits' => $this->relocation_benefits,
            'slug' => $this->slug,
            'updated_at' => $this->updated_at,
            'skills' => $this->getSkills()
        ];

    }

    protected function getSkills(): array
    {
        $out = [];
        $tmp = [];
        $extra = [];
        foreach ($this->skills as $skill) {
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
}
