<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellingProductRequest extends FormRequest
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
            'sub_category_id' => 'required',
            'name' => 'required',
            'description' => 'required',
            'condition' => 'required',
            'price' => 'required',
            'quantity' => 'required',
        ];
    }
}
