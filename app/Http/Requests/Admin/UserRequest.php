<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

class UserRequest extends FormRequest
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
            'email' => 'required|unique:users,email',
            'roles' => 'array',
            'roles.*' => [Rule::exists('roles', 'id')->withoutTrashed()],
            'department_id' => [Rule::exists('departments', 'id')->withoutTrashed()],
        ];

        if ($this->id) {
            unset($rules['name']);
            $rules['email'] = [Rule::unique('users')->ignore($this->id)];
        }

        return $rules;

    }
}
