<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'cliente' => [
                'required',
                'exists:clientes,id',
                Rule::unique('creditos', 'cliente')->where(function ($query) {
                    return $query->where('active', 1);
                }),
            ],
            'credito' => ['required'],
            'cuotas' => ['required'],
            'modalidad' => ['required'],
            'inicio' => ['required'],
            'lugar_cobro' => ['required'],
        ];  
    }
}
