<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserProfileRequest extends FormRequest
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
            'first_name' => 'sometimes|required',
            'last_name' => 'sometimes|required',
            'phone' => 'sometimes|required|numeric',
            'password' => 'sometimes|required|min:8',
            'country_code' => 'sometimes|required',
            'birthdate' => 'nullable',
            'country_id' => 'sometimes|required|numeric',
            'city_id' => 'sometimes|required|numeric',
            'education' => 'nullable',
            'is_wa_as_phone' => 'nullable',
            'whatsapp' => 'nullable',
            'telegram' => 'nullable',
            'language' => 'nullable',
            'link_to_linkedin' => 'nullable',
            'link_to_facebook' => 'nullable',
            'link_to_stackoverflow' => 'nullable',
            'link_to_youtube' => 'nullable',
            'link_to_medium' => 'nullable',
            'link_to_github' => 'nullable',
            'link_to_other' => 'nullable',
            'additionalLanguages' => 'nullable',
            'job_role' => 'sometimes|required',
            'is_journey_finished' => 'sometimes|numeric',
            'profile_picture' => 'sometimes|image|mimes:svg,png,jpg,gif,jpeg|max:5120'
        ];
    }

    /**
     * @return array|string[]
     */
    public function messages(): array
    {
        return [
            'profile_picture.max' => ':attribute can not be empty.',
        ];
    }
}
