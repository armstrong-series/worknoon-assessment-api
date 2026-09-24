<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use App\Traits\HandleJsonApiRequest;

class CreateRefundRequest extends FormRequest
{
    use HandleJsonApiRequest;


    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'order_id' => [
                'required',
                'uuid',
            ],

            'reason' => [
                'required',
                'string',
                'max:5000',
            ],

            'requested_amount_cents' => [
                'required',
                'integer',
                'min:1',
            ],
        ];
    }
}
