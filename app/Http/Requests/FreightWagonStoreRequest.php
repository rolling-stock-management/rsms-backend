<?php

namespace App\Http\Requests;

use App\Models\FreightWagon;
use Illuminate\Foundation\Http\FormRequest;

class FreightWagonStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', FreightWagon::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'number' => ['required', 'digits:12', 'unique:freight_wagons,number'],
            'type_id' => ['required', 'integer', 'exists:freight_wagon_types,id'],
            'letter_marking' => ['sometimes', 'string', 'nullable'],
            'tare' => ['sometimes', 'regex:/^[1-9][\.\d]*(,\d+)?$/', 'nullable'],
            'weight_capacity' => ['sometimes', 'regex:/^[1-9][\.\d]*(,\d+)?$/', 'nullable'],
            'length_capacity' => ['sometimes', 'regex:/^[1-9][\.\d]*(,\d+)?$/', 'nullable'],
            'volume_capacity' => ['sometimes', 'regex:/^[1-9][\.\d]*(,\d+)?$/', 'nullable'],
            'area_capacity' => ['sometimes', 'regex:/^[1-9][\.\d]*(,\d+)?$/', 'nullable'],
            'max_speed' => ['sometimes', 'integer', 'nullable'],
            'length' => ['sometimes', 'regex:/^[1-9][\.\d]*(,\d+)?$/', 'nullable'],
            'brake_marking' => ['sometimes', 'string', 'nullable'],
            'owner_id' => ['required', 'integer', 'exists:owners,id'],
            'status_id' => ['required', 'integer', 'exists:statuses,id'],
            'repair_date' => ['sometimes', 'date', 'nullable'],
            'repair_valid_until' => ['sometimes', 'date', 'nullable'],
            'repair_workshop_id' => ['sometimes', 'integer', 'nullable', 'exists:repair_workshops,id'],
            'depot_id' => ['sometimes', 'integer', 'nullable', 'exists:depots,id'],
            'other_info' => ['sometimes', 'string', 'nullable']
        ];
    }
}
