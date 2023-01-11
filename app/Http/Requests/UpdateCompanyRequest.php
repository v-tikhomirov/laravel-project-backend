<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
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
            'id' => 'sometimes|required',
            'step' => 'nullable',
            'name' => 'sometimes|required',
            'type' => 'sometimes|required',
            'about' => 'sometimes|required',
            'website' => 'sometimes|required',
            'location' => 'sometimes|required',
            'number_of_employees' => 'sometimes|required',
            'country' => 'sometimes|required',
            'city' => 'sometimes|required',
            'branches' => 'sometimes|nullable',
            'benefits' => 'sometimes|nullable',
            'domains' => 'sometimes|nullable',
            'candidates_countries' => 'sometimes|nullable',
            'facebook' => 'nullable',
            'github' => 'nullable',
            'linkedin' => 'nullable',
            'medium' => 'nullable',
            'stackoverflow' => 'nullable',
            'youtube' => 'nullable',
            'logo'  => 'nullable',
            'logoSrc' => 'nullable'
        ];
    }
}
