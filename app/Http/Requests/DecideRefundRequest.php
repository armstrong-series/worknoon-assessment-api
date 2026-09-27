<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\HandleJsonApiRequest;
use Illuminate\Validation\Rule;
use App\Enums\RefundDecision;

class DecideRefundRequest extends FormRequest
{
    use HandleJsonApiRequest;


    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'decision' => [
                'required',
                'string',
                Rule::in([
                    RefundDecision::APPROVED->value,
                    RefundDecision::DENIED->value,
                ]),
            ],
        ];
    }


    public function validatedAttributes(): array
    {
        return $this->validated();
    }
}
