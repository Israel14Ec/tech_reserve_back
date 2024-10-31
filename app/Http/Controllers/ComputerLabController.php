<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ComputerLab;
use App\Http\Requests\ComputerLabStore;
use Illuminate\Support\Facades\Hash;

class ComputerLabController extends Controller
{
    //Creación
    public function create (ComputerLabStore $request) {
        try {
            
            $computerLab = ComputerLab::create($request->all());
            return response()->json([
                'data' => $computerLab,
                'msg' => 'Se agrego el laboratorio'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage(),
                'msg' => 'Algo salió mal no se pudo registrar, inténtelo de nuevo',
            ], 500);
        }
    }

    //Obtener todos 
    public function getAll() {
        try {
            $computerLab= ComputerLab::orderBy('created_at', 'asc')->get();
            return response()->json($computerLab, 200);

        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage(),
                'msg' => 'Algo salió mal no se pudo obtener los datos',
            ], 500);
        }
    }

    //Obtener los computerLabs x id con datos de schedules_lab
    public function getById($id) {
        try {
            $computerLab = ComputerLab::with('schedulesLabs') 
                ->find($id);

            if (!$computerLab) {
                return response()->json(['msg' => 'Laboratorio no encontrado'], 404);
            }

            return response()->json($computerLab);
            
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage(),
                'msg' => 'Algo salió mal no se pudo obtener los datos',
            ], 500);
        }
    }

    //Edición
    public function update (ComputerLabStore $request, $id) {
        try {

            $computerLab = ComputerLab::find($id);

            if(!$computerLab) {
                return response()->json(['msg' => 'No se encontró el laboratorio'], 404);
            }

            $computerLab->update($request->all());
            return response()->json(['msg' => 'Laboratorio actualizado', 'data' =>$computerLab]);

        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage(),
                'msg' => 'Algo salió mal no se pudo actualizar, inténtelo de nuevo',
            ], 500);
        }
    }

    //Eliminado lógico
    public function deleteSoft ($id) {
        try {
            
            $computerLab = ComputerLab::find($id);
            
            if(!$computerLab) {
                return response()->json(['msg' => 'No se encontro el laboratorio'], 404);
            }

            $computerLab->delete(); //Soft delete

            return response()->json([
                'msg' => 'Se elimino el laboratorio',
                'data' => $computerLab
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage(),
                'msg' => 'Algo salió mal no se pudo eliminar, inténtelo de nuevo',
            ], 500);
        }
    }

    //Restaurar
    public function restartLab($id) {
        try {
            $computerLab = ComputerLab::withTrashed()->find($id); //Buscar incluyendo eliminados

            if(!$computerLab) {
                return response()->json(['msg' => 'No se enecontro'] , 404);
            }
            $computerLab->restore();
            return response()->json(
                ['msg' => 'Se restauro el laboratorio',
                 'data' => $computerLab]
            );

        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage(),
                'msg' => 'Algo salió mal no se pudo restaurar, inténtelo de nuevo',
            ], 500);
        }
    }

    //Eliminado permanente
    public function delete(Request $request, $id) {
        try {
            $user = $request->user();

            //Validar el rol 
            if($user->role !== 'admin') {
                return response()->json(['msg' => 'No tienes permisos para realizar esta acción'], 403);
            } 

            //Validar la contraseña
            if (!Hash::check($request->password, $user->password)) {
                return response()->json(['msg' => 'La contraseña es incorrecta'], 403);
            }

            //Buscar
            $computerLab = ComputerLab::withTrashed()->find($id);

            if (!$computerLab) {
                return response()->json(['msg' => 'No se encontró el laboratorio'], 404);
            }

            // Eliminar permanentemente el laboratorio
            $computerLab->forceDelete();

            return response()->json(['msg' => 'Laboratorio eliminado permanentemente']);

        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage(),
                'msg' => 'Algo salió mal no se pudo eliminar, inténtelo de nuevo',
            ], 500);
        }
    }
}
