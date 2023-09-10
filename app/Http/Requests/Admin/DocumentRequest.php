<?php

namespace App\Http\Requests\Admin;

use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class DocumentRequest extends FormRequest
{
    /*
     *
     * department should not be selected twice, once selected should be removed from available department
     * role should not be selected twice, once selected should be removed from available department
     *
     * changeAccess(department_id, role_id, 'view', value){
     *      documentAccesses.find(access => {
     *                       access.department_id === department_id && access.role_id === role_id
     *                   })
     *  }
     *
     * // all departments, all roles
     * [
     *      {
     *          "all_departments" : true,
     *          "all_roles" : true,
     *          "view" : true,
     *          "delete" : true,
     *          "download" : true,
     *          "update" : true,
     *      }
     * ]
     *
     * // all departments, many roles
     * [
     *      {
     *          "all_departments" : true,
     *          "role_id" : 1,
     *          "view" : true,
     *          "delete" : true,
     *          "download" : true,
     *          "update" : true,
     *      },
     *      {
     *          "all_departments" : true,
     *          "role_id" : 2,
     *          "view" : true,
     *          "delete" : false,
     *          "download" : false,
     *          "update" : true,
     *      },
     *      {
     *          "all_departments" : true,
     *          "role_id" : 3,
     *          "view" : true,
     *          "delete" : false,
     *          "download" : false,
     *          "update" : true,
     *      },
     *  ]
     *
     * // individual departments, all roles
     * [
     *      {
     *          "department_id" : 1,
     *          "all_roles" : true,
     *          "view" : true,
     *          "delete" : true,
     *          "download" : true,
     *          "update" : true,
     *      },
     *      {
     *          "department_id" : 2,
     *          "all_roles" : true,
     *          "view" : true,
     *          "delete" : true,
     *          "download" : true,
     *          "update" : true,
     *      },
     * ]
     *
     * // individual departments, many roles
     * [
     *      {
     *          "department_id" : 1,
     *          "role_id" : 5,
     *          "view" : true,
     *          "delete" : true,
     *          "download" : true,
     *          "update" : true,
     *      },
     *      {
     *          "department_id" : 1,
     *          "role_id" : 7,
     *          "view" : true,
     *          "delete" : true,
     *          "download" : true,
     *          "update" : true,
     *      },
     *      {
     *          "department_id" : 2,
     *          "role_id" : 5,
     *          "view" : true,
     *          "delete" : true,
     *          "download" : true,
     *          "update" : true,
     *      },
     *      {
     *          "department_id" : 2,
     *          "role_id" : 7,
     *          "view" : true,
     *          "delete" : true,
     *          "download" : true,
     *          "update" : true,
     *      },
     * ]
     *
     */
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
