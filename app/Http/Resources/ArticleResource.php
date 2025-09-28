<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'content'     => $this->content,
            'url'         => $this->url,
            'image'       => $this->url_to_image,
            'source'      => $this->source?->name,
            'author'      => $this->author?->name,
            'categories'  => $this->categories->pluck('name'),
            'published_at'=> $this->published_at?->toIso8601String(),
        ];
    }
}
