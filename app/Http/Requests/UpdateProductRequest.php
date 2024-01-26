<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'product_name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'section_id' => ['required'],
        ];
    }
    public function messages(): array
    {
        return [
            'product_name.required' => 'يرجى إدخال اسم المنتج',
            'product_name.string' => 'اسم القسم  يجب ان يكون سلسلة محارف',
            'product_name.max' => 'اسم القسم يجب الا يتجاوز 255 حرف',
            'description.required' => 'يرجى إدخال الوصف',
            'section_id.required' => 'يرجى اختيار اسم القسم'
        ];
    }
}
