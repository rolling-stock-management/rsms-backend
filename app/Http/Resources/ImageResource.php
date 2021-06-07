<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => [
                'id' => $this->id,
                'file_name' => $this->file_name,
                'title' => $this->title,
                'description' => $this->description,
                'date' => isset($this->date) ? $this->date->format('d.m.Y') : null,
                // TODO: Add relationship
                'last_updated' => $this->updated_at->format('d.m.Y h:i:s')
            ]
        ];
    }
}
