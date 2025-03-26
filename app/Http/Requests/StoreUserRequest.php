<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'cpf' => 'required|string|size:11|unique:users,cpf',
            'birth_date' => 'required|date_format:Y-m-d',
            'email' => 'required|email|unique:users,email',
            'google_id' => 'required|string|unique:users,google_id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'cpf.required' => 'O campo CPF é obrigatório.',
            'cpf.size' => 'O CPF deve ter exatamente 11 caracteres.',
            'cpf.unique' => 'Este CPF já está em uso.',
            'birth_date.required' => 'O campo data de nascimento é obrigatório.',
            'birth_date.date_format' => 'A data de nascimento deve estar no formato AAAA-MM-DD.',
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'O email deve ser um endereço de email válido.',
            'email.unique' => 'Este email já está em uso.',
            'google_id.required' => 'O campo Google ID é obrigatório.',
            'google_id.unique' => 'Este Google ID já está em uso.',
        ];
    }
}
