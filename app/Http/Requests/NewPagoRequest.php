<?php

namespace App\Http\Requests;

use App\Models\Creditos;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NewPagoRequest extends FormRequest
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
     * @return array<string
     */
    public function rules(): array
    {
        return [
            'cliente' => [
                'required',
                'exists:creditos,cliente',
            ],
            'valor' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    $credito = Creditos::where('cliente', $this->cliente)->where('active', 1)->first();

                    if ($credito && $value > $credito->pago_restante) {
                        $fail("El valor del pago no puede ser mayor que el pago restante de {$credito->pago_restante}.");
                    }
                }
            ],
            'pago_fecha' => ['required', 'date'],
        ];
    }
}
