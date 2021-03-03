<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TractiveUnitUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $tractiveUnit = $this->route('tractive_unit');

        return $this->user()->can('update', $tractiveUnit);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'number' => ['required', 'digits:12', Rule::unique('tractive_units')->ignore($this->tractive_unit)],
            'max_speed' => ['sometimes', 'integer', 'nullable'],
            'power_output' => ['sometimes', 'integer', 'nullable'],
            'tractive_effort' => ['sometimes', 'integer', 'nullable'],
            'weight' => ['sometimes', 'integer', 'nullable'],
            'axle_arrangement' => ['sometimes', 'string', 'nullable'],
            'length' => ['sometimes', 'regex:/^[1-9][\.\d]*(,\d+)?$/', 'nullable'], //Regex for decimal values
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
