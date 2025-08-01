<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'access_type' => $this->accessType->name ?? null,
            'genre_type' => $this->genre->name ?? null,
            'title' => $this->title,
            'description' => $this->description,
            'bunny_video_id' => $this->bunny_video_id,
            'views' => $this->views,
            'tags' => is_string($this->tags) ? json_decode($this->tags, true) : $this->tags,
            'status' => $this->status,
        ];
    }
}
