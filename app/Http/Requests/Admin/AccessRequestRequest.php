<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class AccessRequestRequest extends FormRequest
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
            'id' => [
                Rule::exists('access_requests', 'id')->withoutTrashed(),
            ],
            'document_id' => [
                'required',
                Rule::exists('documents', 'id')->withoutTrashed(),
            ],
            'description' => '',
//            'type_id' => ''
        ];

        if ($this->accessRequest || $this->id) {
            $rules['document_id'] = [ Rule::exists('documents', 'id')->withoutTrashed() ];
        }

        return $rules;

    }

}
