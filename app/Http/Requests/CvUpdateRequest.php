<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CvUpdateRequest extends FormRequest
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
            'id' => 'required',
            'primary.position' => 'required_if:step,primary',
            'primary.industry' => 'required_if:step,primary',
            'primary.type' => 'required_if:step,primary',
            'primary.office_type' => 'required_if:step,primary',
            'primary.status' => 'required_if:step,primary',
            'primary.is_ready_to_relocate' => 'nullable',
            'primary.languages' => 'required_if:step,primary',
            'primary.currency' => 'required_if:step,primary',
            'primary.desired_salary' => 'required_if:step,primary',
            'primary.minimal_salary' => 'required_if:step,primary',
            'primary.about' => 'nullable',
            'skills' => 'required_if:step,skills',
            'recent_projects' => 'required_if:step,recent_projects'
        ];
    }
}
