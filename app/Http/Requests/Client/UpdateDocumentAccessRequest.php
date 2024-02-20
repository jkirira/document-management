<?php

namespace App\Http\Requests\Client;

use App\Rules\IsNormalAccess;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentAccessRequest extends FormRequest
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
            'access.*.id' => [
                'required',
                Rule::exists('document_access', 'id')->withoutTrashed(),
                new IsNormalAccess,
            ],
//            'access.*.all_roles' => 'boolean|prohibits:role_id',
//            'access.*.all_departments' => 'boolean|prohibits:department_id',
//            'access.*.department_id' => [
//                'nullable',
//                Rule::exists('departments', 'id')->withoutTrashed(),
//                'prohibits:all_departments',
//            ],
//            'access.*.role_id' => [
//                'nullable',
//                Rule::exists('roles', 'id')->withoutTrashed(),
//                'prohibits:all_roles',
//            ],
            'access.*.user_id' => 'prohibited',
            'access.*.update' => 'required|boolean',
            'access.*.view' => 'required|boolean',
            'access.*.delete' => 'required|boolean',
            'access.*.download' => 'required|boolean',
        ];

        return $rules;

    }

}
