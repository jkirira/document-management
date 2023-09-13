<?php

namespace App\Http\Requests\Admin;

use App\Rules\IsSpecialUserAccess;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserAccessRequest extends FormRequest
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
        $rules = [
            'access' => 'array',
            'access.*.all_roles' => 'prohibited',  // should not be,
            'access.*.all_departments' => 'prohibited',  // should not be,
            'access.*.department_id' => 'prohibited',   // should not be
            'access.*.role_id' => 'prohibited', // should not be
            'access.*.id' => [
//                'required',
                Rule::exists('document_access', 'id')->withoutTrashed(),
                new IsSpecialUserAccess,
            ],
            'access.*.user_id' => [
                'required_without:access.*.id',
                Rule::exists('users', 'id')->withoutTrashed(),
            ],
            'access.*.update' => 'required|boolean',
            'access.*.view' => 'required|boolean',
            'access.*.delete' => 'required|boolean',
            'access.*.download' => 'required|boolean',
        ];

        return $rules;

    }

}
