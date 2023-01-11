<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DraftCvRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return !!auth()->user();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => 'required',
            'id' => 'sometimes|required',
            'primary.position' => 'nullable',
            'primary.industry' => 'nullable',
            'primary.type' => 'nullable',
            'primary.office_type' => 'nullable',
            'primary.status' => 'nullable',
            'primary.is_ready_to_relocate' => 'nullable',
            'primary.languages' => 'nullable',
            'primary.currency' => 'nullable',
            'primary.desired_salary' => 'nullable',
            'primary.minimal_salary' => 'nullable',
            'primary.about' => 'nullable',
            'skills' => 'nullable',
            'recent_projects' => 'nullable'
        ];
    }
}
