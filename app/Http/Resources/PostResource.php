<?php

namespace App\Http\Resources;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class PostResource extends JsonResource
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
            'title' => $this->title,
            'body' => $this->body,
            'cover_image' => $this->cover_image,
            'tags' => TagResource::collection($this->tags),
        ];
    }
}
