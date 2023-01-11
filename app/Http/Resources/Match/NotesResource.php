<?php

namespace App\Http\Resources\Match;

use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\DataPreparation;

class NotesResource extends JsonResource
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
            'match_id' => $this->match_id,
            'user_id' => $this->user_id,
            'message' => $this->message,
            'created_at' => $this->created_at,
            'is_deleted' => $this->is_deleted,
            'user' => UserResource::make($this->user)->resolve()
        ];
    }
}
