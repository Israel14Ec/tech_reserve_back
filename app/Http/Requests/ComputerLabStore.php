<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class ComputerLabStore extends FormRequest
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
            'name' => 'required|string',
            'ability' => 'required|integer|min:1', 
            'equipment' => 'required|integer|min:1', 
            'location' => 'required|string'
        ];
        
    }

    public function messages() : array{
        return [
            'name.required' => 'Ingrese el nombre del laboratorio',
            'ability.required' =>'Ingrese la disponibilidad',
            'ability.integer' => 'La disponibilidad debe ser un número',
            'ability.min' => 'Ingrese una disponibilidad mínimo de 1',
            'equipment.required' =>'Ingrese la disponibilidad de equipos',
            'equipment.integer' => 'La cantidad de equipos debe ser un número',
            'equipment.min' => 'La cantidad de equipos debe ser mínimo de 1',
            'location.required' => 'Ingrese una locación'
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
