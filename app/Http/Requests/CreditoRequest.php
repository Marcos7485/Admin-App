<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreditoRequest extends FormRequest
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
            'cliente' => ['required', 'exists:clientes,id'],
            'credito' => ['required'],
            'cuotas' => ['required'],
            'modalidad' => ['required'],
            'inicio' => ['required'],
            'lugar_cobro' => ['required']
        ];
    }
}
