<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

class MeetingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'start_time' => $this->start_time->toIso8601String(),
            'end_time' => $this->end_time->toIso8601String(),
            'duration' => $this->duration,
            'status' => $this->status,
            'type' => $this->type,
            'join_url' => $this->when($this->canJoin(), $this->join_url),
            'participants' => ParticipantResource::collection($this->whenLoaded('participants')),
            'documents' => DocumentResource::collection($this->whenLoaded('documents')),
            'recording' => new RecordingResource($this->whenLoaded('recording')),
            'permissions' => [
                'can_join' => $this->canJoin(),
                'can_edit' => auth()->user()->can('update', $this->resource),
                'can_delete' => auth()->user()->can('delete', $this->resource)
            ],
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String()
        ];
    }
}