<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class HomeCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'banners' => BannerResource::collection($this['banners']),
            'latest' => VideoResource::collection($this['videos']),
            'most-popular' => VideoResource::collection($this['most_popular'])
        ];
    }
}
