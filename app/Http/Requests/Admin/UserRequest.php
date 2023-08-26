<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        if ($this->id) {
            return [
                // ignore this id
                'email' => Rule::unique('users')->ignore($this->id),
                'roles' => 'array',
                'roles.*' => 'exists:roles,id',
            ];
        } else {
            return [
                'name' => 'required',
                'email' => 'required|unique:users,email',
                'roles' => 'array',
                'roles.*' => 'exists:roles,id',
            ];
        }
    }
}
