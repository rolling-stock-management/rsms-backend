<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PassengerWagonUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $passengerWagon = $this->route('passenger_wagon');

        return $this->user()->can('update', $passengerWagon);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'number' => ['required', 'digits:12', Rule::unique('passenger_wagons')->ignore($this->passenger_wagon)],
            'letter_marking' => ['sometimes', 'string', 'nullable'],
            'tare' => ['sometimes', 'integer', 'nullable'],
            'total_weight' => ['sometimes', 'integer', 'nullable'],
            'seats_count' => ['sometimes', 'integer', 'nullable'],
            'max_speed' => ['sometimes', 'integer', 'nullable'],
            'length' => ['sometimes', 'regex:/^[1-9][\.\d]*(,\d+)?$/', 'nullable'], //Regex for decimal values
            'brake_marking' => ['sometimes', 'string', 'nullable'],
            'owner_id' => ['required', 'integer', 'exists:owners,id'],
            'status_id' => ['required', 'integer', 'exists:statuses,id'],
            'repair_date' => ['sometimes', 'date', 'nullable'],
            'repair_workshop_id' => ['sometimes', 'integer', 'nullable', 'exists:repair_workshops,id'],
            'depot_id' => ['sometimes', 'integer', 'nullable', 'exists:depots,id'],
            'other_info' => ['sometimes', 'string', 'nullable']
        ];
    }
}
