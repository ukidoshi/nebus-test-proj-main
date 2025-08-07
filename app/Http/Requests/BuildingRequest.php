<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BuildingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'address' => ['required', 'string'],
            'lat' => ['required', 'numeric:'],
            'lng' => ['required', 'numeric'],
        ];
    }
}
