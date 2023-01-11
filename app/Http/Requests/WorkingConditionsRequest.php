<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkingConditionsRequest extends FormRequest
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
            'currency' => 'required',
            'desired_salary' => 'required',
            'is_ready_to_relocate' => 'required',
            'minimal_salary' => 'required',
            'office_type' => 'required',
            'position' => 'required',
            'status' => 'required'
        ];
    }
}
