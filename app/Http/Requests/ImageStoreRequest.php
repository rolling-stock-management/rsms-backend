<?php

namespace App\Http\Requests;

use App\Models\Image;
use App\Rules\ImageablesArrayRules;
use Illuminate\Foundation\Http\FormRequest;

class ImageStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', Image::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => ['required', 'string'],
            'description' => ['sometimes', 'string'],
            'date' => ['sometimes', 'date'],
            'file' => ['required', 'image', 'mimes:jpeg,bmp,png,gif,webp'],
            'imageables' => ['required', new ImageablesArrayRules],
        ];
    }
}
