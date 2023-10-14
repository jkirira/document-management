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
            'all_roles' => 'prohibited',  // should not be,
            'all_departments' => 'prohibited',  // should not be,
            'department_id' => 'prohibited',   // should not be
            'role_id' => 'prohibited', // should not be
            'id' => [
//                'required',
                Rule::exists('document_access', 'id')->withoutTrashed(),
                new IsSpecialUserAccess,
            ],
            'user_id' => [
                'required_without:id',
                Rule::exists('users', 'id')->withoutTrashed(),
            ],
            'document_id' => [
                'required',
                Rule::exists('documents', 'id')->withoutTrashed(),
            ],
            'update' => 'required|boolean',
            'view' => 'required|boolean',
            'delete' => 'required|boolean',
            'download' => 'required|boolean',
        ];

        return $rules;

    }

}
