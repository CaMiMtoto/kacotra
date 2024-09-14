<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ValidateCreateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'numeric', 'exists:customers,id'],
            "payment_type" => ['required'],
            "pay" => ['required', 'numeric',
                // validate pay to be greater than 0 if payment type is not Due
                function ($attribute, $value, $fail) {
                    if ($this->payment_type != 'Due' && $value <= 0) {
                        $fail('The amount to pay must be greater than 0.');
                    }
                },
                // validate pay to be equal to 0 if payment type is Due
                function ($attribute, $value, $fail) {
                    if ($this->payment_type == 'Due' && $value != 0) {
                        $fail('The amount to pay must be 0.');
                    }
                },

            ],
        ];
    }
}
