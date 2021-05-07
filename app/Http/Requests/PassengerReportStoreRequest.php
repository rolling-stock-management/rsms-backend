<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PassengerReportStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => ['required', 'email'],
            'date' => ['required', 'date'],
            'problem_description' => ['required', 'string'],
            'wagon_number' => ['required', 'integer'],
            'train_id' => ['required', 'integer', 'exists:App\Models\Train,id'],
            'image' => ['sometimes', 'nullable', 'image', 'mimes:jpeg,bmp,png,gif,webp']
        ];
    }
}
