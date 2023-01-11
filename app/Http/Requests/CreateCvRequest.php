<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCvRequest extends FormRequest
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
            'primary.position' => 'required',
            'primary.industry' => 'required',
            'primary.type' => 'required',
            'primary.office_type' => 'required',
            'primary.status' => 'required',
            'primary.is_ready_to_relocate' => 'nullable',
            'primary.languages' => 'required',
            'primary.currency' => 'required',
            'primary.desired_salary' => 'required',
            'primary.minimal_salary' => 'required',
            'primary.about' => 'nullable',
            'skills' => 'required',
            'recent_projects' => 'required'
        ];
//        return [
//            'position' => 'required',
//            'office_type' => 'required',
//            'is_ready_to_relocate' => 'required',
//            'currency' => 'required',
//            'minimal_salary' => 'required',
//            'desired_salary' => 'required',
//            'status' => 'required',
//            'about' => 'required',
//            'link_to_linkedin' => 'nullable',
//            'link_to_facebook' => 'nullable',
//            'link_to_stackoverflow' => 'nullable',
//            'link_to_youtube' => 'nullable',
//            'link_to_medium' => 'nullable',
//            'link_to_github' => 'nullable',
//            'skills' => 'required',
//        ];
    }
}
