<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;


class UserStore extends FormRequest
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
            'name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'cell_number' => 'required|max:10|unique:users',
            'email' => 'email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|in:admin,teacher', //validación del campo enum
        ];
    }


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Ingrese un nombre',
            'name.max' => 'El nombre no puede exceder los 100 caracteres',
            'last_name.required' => 'Ingrese un apellido',
            'last_name.max' => 'El apellido no puede exceder los 100 caracteres',
            'cell_number.required' => 'Ingrese un número de celular',
            'cell_number.max' => 'El número de celular no puede exceder los 10 caracteres',
            'cell_number.unique' => 'El número ya fue registrado',
            'email.email' => 'Ingrese un correo electrónico válido',
            'email.unique' => 'El email ya fue registrado',
            'password.required' => 'Ingrese una contraseña',
            'password.min' => 'La contraseña debe ser mínimo de 8 caracteres',
            'role.required' => 'Seleccione un rol',
            'role.in' => 'El rol seleccionado no es válido',
        ];
    }

    protected function failedValidation(Validator $validator) {
        $errors = $validator->errors()->all();
    
        throw new HttpResponseException(
            response()->json([
                'msg' => implode(', ', $errors),
            ], 422)
        );
    }
    
}
