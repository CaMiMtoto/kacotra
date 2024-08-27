<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidatePayDueOrderRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'numeric', 'exists:orders,id'],
            'pay' => ['required', 'numeric',
           /*     function ($attribute, $value, $fail) {
                    $order = \App\Models\Order::find($this->id);
                    if ($order && $value > $order->due) {
                        $fail('The pay amount must not exceed the due amount. The current due amount is ' . $order->due . ' Rwf.');
                    }
                }*/
            ],
            'payment_type' => ['required', 'string'],
            'comment' => ['nullable', 'string'],
        ];
    }


    public function messages(): array
    {
        return [
            'pay.numeric' => 'The pay amount must be a number.',
            'pay.required' => 'The pay amount is required.',
            'payment_type.required' => 'The payment type is required.',
            'id.required' => 'The order id is required.',
            'id.numeric' => 'The order id must be a number.',
        ];
    }
}
