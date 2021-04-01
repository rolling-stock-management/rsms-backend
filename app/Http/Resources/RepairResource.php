<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RepairResource extends JsonResource
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
                'short_description' => $this->short_description,
                'type' => $this->type,
                'workshop' => $this->workshop,
                'repairable' => $this->repairable,
                'description' => $this->description,
                'start_date' => $this->start_date ? $this->start_date->format('d.m.Y') : null,
                'end_date' => $this->end_date ? $this->end_date->format('d.m.Y') : null,
                'created_at' => $this->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $this->updated_at->format('d.m.Y h:i:s')
            ]
        ];
    }
}
