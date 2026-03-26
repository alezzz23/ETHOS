<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'phone'      => ['nullable', 'string', 'max:30'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'position'   => ['nullable', 'string', 'max:120'],
            'bio'        => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'       => 'El nombre es obligatorio.',
            'email.required'      => 'El correo electrónico es obligatorio.',
            'email.email'         => 'Ingresa un correo válido.',
            'email.unique'        => 'Este correo ya está en uso.',
            'phone.max'           => 'El teléfono no puede tener más de 30 caracteres.',
            'birth_date.date'     => 'Ingresa una fecha válida.',
            'birth_date.before'   => 'La fecha de nacimiento debe ser una fecha pasada.',
            'position.max'        => 'El cargo no puede tener más de 120 caracteres.',
            'bio.max'             => 'La biografía no puede superar los 500 caracteres.',
        ];
    }
}
