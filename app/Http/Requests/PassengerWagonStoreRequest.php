<?php

namespace App\Http\Requests;

use App\Models\PassengerWagon;
use Illuminate\Foundation\Http\FormRequest;

class PassengerWagonStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', PassengerWagon::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'number' => ['required', 'digits:12', 'unique:passenger_wagons,number'],
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
