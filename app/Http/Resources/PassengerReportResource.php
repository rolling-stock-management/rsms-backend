<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PassengerReportResource extends JsonResource
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
                'email' => $this->email,
                'date' => $this->date->format('Y-m-d'),
                'problem_description' => $this->problem_description,
                'wagon_number' => $this->wagon_number,
                'train' => new TrainResource($this->train),
                'wagon' => new PassengerWagonResource($this->wagon),
                'created_at' => $this->created_at->format('d.m.Y h:i:s'),
                'updated_at' => $this->updated_at->format('d.m.Y h:i:s')
            ]
        ];
    }
}
