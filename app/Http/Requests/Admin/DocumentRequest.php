<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class DocumentRequest extends FormRequest
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
            'name' => 'required',
            'document'  => 'required|max:2048',
            'folder_id' => [
                'nullable',
                Rule::exists('folders', 'id')->withoutTrashed(),
            ],
            'access' => 'array',
            'access.*.all_departments' => 'boolean',
            'access.*.all_roles' => 'boolean',
            'access.*.department_id' => [
                'nullable',
                Rule::exists('departments', 'id')->withoutTrashed(),
                'required_without:access.*.all_departments',
                'required_if:access.*.all_departments,false',
            ],
            'access.*.role_id' => [
                'nullable',
                Rule::exists('roles', 'id')->withoutTrashed(),
                'required_without:access.*.all_roles',
                'required_if:access.*.all_roles,false',
            ],
            'access.*.update' => 'required|boolean',
            'access.*.view' => 'required|boolean',
            'access.*.delete' => 'required|boolean',
            'access.*.download' => 'required|boolean',
        ];


        if ($this->id) {
            return [
                'name' => 'required',
            ];
        }

        return $rules;

    }
}
