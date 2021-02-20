<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
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
                'permissions' => PermissionResource::collection($this->permissions()->get()),
                'last_updated' => $this->updated_at->format('d.m.Y h:i:s')
            ]
        ];
    }
}
