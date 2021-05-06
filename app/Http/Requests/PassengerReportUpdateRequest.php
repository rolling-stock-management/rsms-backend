<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PassengerReportUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $passengerReport = $this->route('passenger_report');

        return $this->user()->can('update', $passengerReport);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date' => ['required', 'date'],
            'problem_description' => ['required', 'string'],
            'wagon_number' => ['required', 'integer'],
            'train_id' => ['required', 'integer', 'exists:App\Models\Train,id'],
            'wagon_id' => ['sometimes', 'integer', 'exists:App\Models\PassengerWagon,id', 'nullable']
        ];
    }
}
