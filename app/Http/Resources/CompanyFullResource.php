<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyFullResource extends JsonResource
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
            'name' => $this->name,
            'type' => $this->getTypeArray(),
            'website' => $this->website,
            'number_of_employees' => $this->number_of_employees,
            'location' => [
                'city' => $this->city_id,
                'country' => $this->country_id
            ],
            'about' => $this->about,
            'benefits' => $this->getBenefitsArray(),
            'linkedin' => $this->link_to_linkedin,
            'github' => $this->link_to_github,
            'medium' => $this->link_to_medium,
            'youtube' => $this->link_to_youtube,
            'stackoverflow' => $this->link_to_stackoverflow,
            'facebook' => $this->link_to_facebook,
            'logo' => $this->logo,
            'domains' => $this->getDomainsArray(),
            'balance' => $this->balance?->amount
        ];
    }

    protected function getTypeArray(): array
    {
        return explode(';', $this->type);
    }

    protected function getBenefitsArray(): array
    {
        $out = [];
        $benefits = $this->benefits;
        if($this->benefits) {
            foreach ($this->benefits as $benefit) {
                $out[] = $benefit->id;
            }
            return $out;
        }
        return Array();
    }

    protected function getDomainsArray(): array
    {
        $out = [];
        if($this->domains) {
            foreach ($this->domains as $domain) {
                $out[] = $domain->id;
            }
            return $out;
        }
        return Array();
    }
}
