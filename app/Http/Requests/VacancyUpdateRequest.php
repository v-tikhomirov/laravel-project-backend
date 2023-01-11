<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VacancyUpdateRequest extends FormRequest
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
            'step' => 'required',
            'status' => 'required',
            'id' => 'required',
            'general.position' => 'required_if:step,general',
            'general.industry' => 'required_if:step,general',
            'general.office_type' => 'required_if:step,general',
            'general.is_ready_to_relocate' => 'required_if:step,general',
            'general.relocation_benefits' => 'nullable',
            'general.location' => 'required_if:step,general',
            'general.languages' => 'required_if:step,general',
            'general.currency' => 'required_if:step,general',
            'general.max_salary' => 'required_if:step,general',
            'general.desired_salary' => 'required_if:step,general',
            'general.description' => 'required_if:step,general',
            'general.benefits' => 'required_if:step,general',
            'skills' => 'required_if:step,skills',
            'about' => 'required_if:step,about'
        ];
    }
}
