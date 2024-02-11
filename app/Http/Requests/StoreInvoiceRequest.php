<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'Section' => ['required', 'exists:sections,id'],
            'invoice_number' => ['required', 'string', 'max:255'],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:invoice_date'],
            'product' => ['required', 'string', 'max:255'],
            'collection_amount' => ['required', 'numeric', 'min:0'],
            'commission_amount' => ['required', 'numeric', 'min:0'],
            'discount' => ['required', 'numeric', 'min:0'],
            'rate_vat' => ['required', 'string', 'max:255'],
            'value_vat' => ['required', 'numeric', 'min:0'],
            'Total' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
            'payment_date' => ['nullable', 'date', 'after_or_equal:invoice_date'],
            'pic' => 'nullable|mimes:pdf,jpg,png,jpeg|max:2048',
        ];
    }
}
