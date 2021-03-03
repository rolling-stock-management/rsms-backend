<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RepairWorkshopResource extends JsonResource
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
                'name' => $this->name,
                'abbreviation' => $this->abbreviation,
                'note' => $this->note,
                'created_at' => $this->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $this->updated_at->format('d.m.Y h:i:s')
            ]
        ];
    }
}
