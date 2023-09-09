<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class DocumentAccessRequest extends FormRequest
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
            'all_departments' => 'boolean',
            'all_roles' => 'boolean',
            'department_id' => [
                'nullable',
                Rule::exists('departments', 'id')->withoutTrashed(),
                'required_without:all_departments',
                'required_if:all_departments,false',
            ],
            'role_id' => [
                'nullable',
                Rule::exists('roles', 'id')->withoutTrashed(),
                'required_without:all_roles',
                'required_if:all_roles,false',
            ],
            'update' => 'required|boolean',
            'view' => 'required|boolean',
            'delete' => 'required|boolean',
            'download' => 'required|boolean',
        ];

        return $rules;

    }

}
