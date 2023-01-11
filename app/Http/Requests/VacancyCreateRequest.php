<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VacancyCreateRequest extends FormRequest
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
            'company_id' => 'required',
            'status' => 'required',
            'general.position' => 'required',
            'general.industry' => 'required',
            'general.office_type' => 'required',
            'general.is_ready_to_relocate' => 'nullable',
            'general.relocation_benefits' => 'nullable',
            'general.location' => 'required',
            'general.languages' => 'required',
            'general.currency' => 'required',
            'general.max_salary' => 'required',
            'general.desired_salary' => 'required',
            'general.description' => 'required',
            'general.benefits' => 'required',
            'skills' => 'nullable',
            'about' => 'nullable'
        ];
    }
}
