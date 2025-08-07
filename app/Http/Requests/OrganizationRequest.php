<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrganizationRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => ['required'],
            'building_id' => ['required', 'integer']
        ];
    }

    public function authorize()
    {
        return true;
    }
}
