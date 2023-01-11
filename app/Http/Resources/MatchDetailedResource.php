<?php

namespace App\Http\Resources;

use App\Http\Resources\Match\CandidateDetailedResource;
use App\Http\Resources\Match\CvDetailedResource;
use App\Http\Resources\Match\NotesResource;
use App\Http\Resources\Match\VacancyDetailedResource;
use Illuminate\Http\Resources\Json\JsonResource;

class MatchDetailedResource extends JsonResource
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
            'user_id' => $this->user_id,
            'company_id' => $this->company_id,
            'cv_id' => $this->cv_id,
            'vacancy_id' => $this->vacancy_id,
            'status' => $this->status,
            'percent' => $this->percent,
            'decline_reason' => $this->decline_reason,
            'created_at' => $this->created_at,
            'cv' => CvDetailedResource::make($this->cv)->resolve(),
            'vacancy' => VacancyDetailedResource::make($this->vacancy)->resolve(),
            'company' => CompanyFullResource::make($this->company)->resolve(),
            'candidate' => CandidateDetailedResource::make($this->candidate)->resolve(),
            'interviews' => $this->interviews,
            'notes' => NotesResource::collection($this->notes)->resolve(),
            'history' => MatchHistoryResource::collection($this->history)->resolve()
        ];
    }
}
