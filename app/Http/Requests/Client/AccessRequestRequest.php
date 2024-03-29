<?php

namespace App\Http\Requests\Client;

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
            'expiry_date' => 'date|after:yesterday',
            'expiry_time' => 'date_format:H:i|required_with:expiry_date',
//            'type_id' => ''
        ];

        if ($this->accessRequest || $this->id) {
            $rules['document_id'] = [ Rule::exists('documents', 'id')->withoutTrashed() ];
        }

        return $rules;

    }

}
