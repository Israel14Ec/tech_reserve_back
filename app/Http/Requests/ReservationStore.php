<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class ReservationStore extends FormRequest
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
            "reservation_date" => "required|date|after_or_equal:today",
            "id_schedules_lab" => "required|exists:schedules_labs,id",
            "id_user" => "required|exists:users,id",
            "type_reservation" => 'required|in:classes,practices,events'
        ];
    }

    public function messages() : array {
        return [
            'reservation_date.required' => 'La fecha de reserva es obligatoria.',
            'reservation_date.date' => 'La fecha de reserva debe ser válida.',
            'reservation_date.after_or_equal' => 'La fecha de reserva no puede ser anterior a hoy.',
            'id_schedules_lab.required' => 'Debe seleccionar un horario.',
            'id_schedules_lab.exists' => 'El horario seleccionado no es válido.',
            'id_user.required' => 'Seleccione a un usuario.',
            'id_user.exists' => 'El usuario seleccionado no es válido.',
            "type_reservation.required" => "Elija el tipo de reservación",
            "type_reservation.in" => 'Escoga un tipo de reservación válida'
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
