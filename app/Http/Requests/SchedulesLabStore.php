<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class SchedulesLabStore extends FormRequest
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
            'start_time' => 'required|date_format:H:i|before:end_time',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'id_computer_labs' => 'required|exists:computer_labs,id',
        ];
    }

    public function messages(): array
    {
        return [
            'start_time.required' => 'La hora de inicio es obligatoria.',
            'start_time.date_format' => 'La hora de inicio debe estar en el formato HH:mm.',
            'start_time.before' => 'La hora de inicio debe ser antes de la hora de fin.',
            'end_time.required' => 'La hora de fin es obligatoria.',
            'end_time.date_format' => 'La hora de fin debe estar en el formato HH:mm.',
            'end_time.after' => 'La hora de fin debe ser después de la hora de inicio.',
            'id_computer_labs.required' => 'Debe seleccionar un laboratorio.',
            'id_computer_labs.exists' => 'El laboratorio seleccionado no existe.',
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
