<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLinksRequest extends FormRequest
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
            'link_to_linkedin' => 'nullable',
            'link_to_facebook' => 'nullable',
            'link_to_stackoverflow' => 'nullable',
            'link_to_youtube' => 'nullable',
            'link_to_medium' => 'nullable',
            'link_to_github' => 'nullable',
        ];
    }
}
