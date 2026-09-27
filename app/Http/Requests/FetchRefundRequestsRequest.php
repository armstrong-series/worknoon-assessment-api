<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FetchRefundRequestsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'per_page' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100',
            ],

            'filter' => [
                'sometimes',
                'array',
            ],

            'filter.status' => [
                'sometimes',
                'string',
                Rule::in([
                    'pending',
                    'processed',
                ]),
            ],

            'filter.decision' => [
                'sometimes',
                'string',
                Rule::in([
                    'approved',
                    'denied',
                    'escalated',
                ]),
            ],

            'filter.reason' => [
                'sometimes',
                'string',
            ],
        ];
    }
}
