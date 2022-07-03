<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostBookRequest extends FormRequest
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
        // @TODO implement
        return [
            'isbn'           => ['required', 'size:13', 'unique:books'],
            'title'          => ['required'],
            'description'    => ['required'],
            'authors'        => ['required', 'array'],
            'authors.*'      => ['required', 'numeric', 'exists:authors,id'],
            'published_year' => ['required', 'numeric', 'min:1900', 'max:2020'],
        ];
    }
}
