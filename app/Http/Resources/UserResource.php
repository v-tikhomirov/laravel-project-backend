<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data =  [
            'id' => $this->id,
            'email' => $this->email,
            'is_invited' => $this->is_invited,
            'type' => $this->type,
            'is_super' => $this->is_super,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'profile'   => $this->profile
        ];
        if($this->type === 'company') {
            $data['companies'] = CompanyFullResource::collection($this->companies)->resolve();
        }
        return $data;
    }
}
