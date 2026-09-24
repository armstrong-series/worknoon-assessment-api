<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\HandleJsonApiRequest;

class LoginRequest extends FormRequest
{
    use HandleJsonApiRequest;



    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],

            'password' => [
                'required',
                'string',
            ],
        ];
    }
}
