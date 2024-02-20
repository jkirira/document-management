<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FolderRequest extends FormRequest
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
            'parent_id' => [
                'nullable',
                Rule::exists('folders', 'id')->withoutTrashed(),
            ],
        ];

        if (isset($this->folder)) {
            array_push($rules['parent_id'], Rule::notIn([$this->folder->id]));
        }

        return $rules;
    }

}
