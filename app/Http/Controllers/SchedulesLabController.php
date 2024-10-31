<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SchedulesLab;
use App\Http\Requests\SchedulesLabStore;

class SchedulesLabController extends Controller
{
    //crear 
    public function create(SchedulesLabStore $request)
    {
        try {
            $idComputerLab = $request->id_computer_labs;
            $startTime = $request->start_time;
            $endTime = $request->end_time;

            // Verificar solapamiento
            $overlappingSchedules = SchedulesLab::where('id_computer_labs', $idComputerLab)
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->where(function ($query) use ($startTime, $endTime) {
                        // Caso 1: El start_time propuesto está entre el start_time y end_time de otro horario
                        $query->where('start_time', '<=', $startTime)
                            ->where('end_time', '>', $startTime);
                    })->orWhere(function ($query) use ($startTime, $endTime) {
                        // Caso 2: El end_time propuesto está entre el start_time y end_time de otro horario
                        $query->where('start_time', '<', $endTime)
                            ->where('end_time', '>=', $endTime);
                    })->orWhere(function ($query) use ($startTime, $endTime) {
                        // Caso 3: El nuevo horario propuesto envuelve completamente un horario existente
                        $query->where('start_time', '>=', $startTime)
                            ->where('end_time', '<=', $endTime);
                    });
                })
                ->exists();

            if ($overlappingSchedules) {
                return response()->json(['msg' => 'El horario seleccionado no es válido.'], 400);
            }

            // Crear el nuevo horario si no hay solapamientos
            $schedulesLab = SchedulesLab::create($request->all());
            $schedulesLab->refresh(); // Recargar para obtener todos los atributos correctamente
            return response()->json([
                'data' => $schedulesLab,
                'msg' => 'Se añadió el horario'
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'msg' => 'Algo salió mal, no se pudo agregar el horario',
                'error' => $th->getMessage()
            ], 500);
        }
    }


    //Actualizar 
    public function update(SchedulesLabStore $request, $id)
    {
        try {
            $idComputerLab = $request->id_computer_labs;
            $startTime = $request->start_time;
            $endTime = $request->end_time;
    
            // Buscar el registro que se va a actualizar
            $schedulesLab = SchedulesLab::find($id);
    
            if (!$schedulesLab) {
                return response()->json(['msg' => 'El horario no existe'], 404);
            }
    
            // Verificar solapamiento, excluyendo el horario que se está actualizando
            $overlappingSchedules = SchedulesLab::where('id_computer_labs', $idComputerLab)
                ->where('id', '<>', $id) // Excluir el registro actual
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->where(function ($query) use ($startTime, $endTime) {
                        // Caso 1: El start_time propuesto está entre el start_time y end_time de otro horario
                        $query->where('start_time', '<=', $startTime)
                            ->where('end_time', '>', $startTime);
                    })->orWhere(function ($query) use ($startTime, $endTime) {
                        // Caso 2: El end_time propuesto está entre el start_time y end_time de otro horario
                        $query->where('start_time', '<', $endTime)
                            ->where('end_time', '>=', $endTime);
                    })->orWhere(function ($query) use ($startTime, $endTime) {
                        // Caso 3: El nuevo horario propuesto envuelve completamente un horario existente
                        $query->where('start_time', '>=', $startTime)
                            ->where('end_time', '<=', $endTime);
                    });
                })
                ->exists();
    
            if ($overlappingSchedules) {
                return response()->json(['msg' => 'El horario seleccionado se solapa con otro horario existente.'], 400);
            }
    
            // Actualizar el horario
            $schedulesLab->update($request->all());
            $schedulesLab->refresh(); // Refrescar los datos actualizados
    
            return response()->json([
                'msg' => 'Horario actualizado exitosamente',
                'data' => $schedulesLab
            ]);
    
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => 'Algo salió mal, no se pudo actualizar la información',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    
    
    //Disponibilidad
    public function availabilityLab ($id) {
        try {
            $schedulesLab = SchedulesLab::find($id);

            if(!$schedulesLab) {
                return response()->json(['msg' => 'No se encontró el laboratorio'],404);
            }

            $schedulesLab->is_availability = !$schedulesLab->is_availability;
            $schedulesLab->save();
            return response()->json(['msg' => 'Disponibilidad actualizada', 'data' => $schedulesLab], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'msg' => 'Algo salió mal no se pudo actualizar la disponibilidad',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
