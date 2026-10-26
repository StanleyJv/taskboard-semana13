<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuardarTransaccionApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'comercio_id' => ['required', 'exists:comercios,id'],
            'cliente_nombre' => ['required', 'string', 'min:3', 'max:255'],
            'monto' => ['required', 'numeric', 'min:0.01'],
            'estado' => ['nullable', 'in:Iniciada,Completada,Fallida'],
        ];
    }

    public function messages(): array
    {
        return [
            'comercio_id.required' => 'Debes indicar un comercio.',
            'comercio_id.exists' => 'El comercio seleccionado no existe.',
            'cliente_nombre.required' => 'Debes indicar el nombre del cliente.',
            'cliente_nombre.min' => 'El nombre del cliente es demasiado corto.',
            'monto.required' => 'Debes indicar un monto.',
            'monto.numeric' => 'El monto debe ser un número.',
            'monto.min' => 'El monto debe ser mayor a cero.',
            'estado.in' => 'El estado indicado no es válido.',
        ];
    }
}
