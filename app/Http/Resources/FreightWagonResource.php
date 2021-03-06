<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FreightWagonResource extends JsonResource
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
                'number' => $this->number,
                'stylized_number' => $this->getStylizedNumber(),
                'type' => new FreightWagonTypeResource($this->type),
                'letter_marking' => $this->letter_marking,
                'tare' => $this->tare,
                'weight_capacity' => $this->weight_capacity,
                'length_capacity' => $this->length_capacity,
                'volume_capacity' => $this->volume_capacity,
                'area_capacity' => $this->area_capacity,
                'max_speed' => $this->max_speed,
                'length' => $this->length,
                'brake_marking' => $this->brake_marking,
                'owner' => new OwnerResource($this->owner),
                'status' => new StatusResource($this->status),
                'repair_date' => $this->repair_date ? $this->repair_date->format('Y-m-d') : null,
                'repair_valid_until' => $this->repair_valid_until ? $this->repair_valid_until->format('Y-m-d') : null,
                'repair_workshop' => new RepairWorkshopResource($this->repairWorkshop),
                'depot' => new DepotResource($this->depot),
                'other_info' => $this->other_info,
                'created_at' => $this->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $this->updated_at->format('d.m.Y h:i:s')
            ]
        ];
    }
}
