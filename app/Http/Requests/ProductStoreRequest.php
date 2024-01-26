<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
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
            'product_name' => ['required', 'max:255'],
            'description' => ['required'],
        ];
    }
    public function messages(): array
    {
        return [
            'product.required' => 'يرجى إدخال اسم القسم',
            'section_name.max' => 'اسم القسم يجب الا يتجاوز 255 حرف',
            'description.required' => 'يرجى إدخال الوصف',
        ];
    }
}
