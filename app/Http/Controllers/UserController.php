<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserStore;
use Illuminate\Support\Facades\Hash;
use App\Notifications\EmailVerification;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    //Crear usuario
    public function create (UserStore $request) {
        try {
            DB::beginTransaction(); // Inicia la transacción
            $validatedData = $request->validated(); //Datos validados
            
            // Hashear la contraseña
            $validatedData['password'] = Hash::make($validatedData['password']);
            $user = User::create($validatedData);
            
            //Generar Token
            $token = Str::uuid();
            $user->email_verification_token = $token;
            $user->save();

            //Enviar el email
            $user->notify(new EmailVerification($user, $token));

            DB::commit(); // Confirma la transacción
            return response()->json(['msg' => 'Se registro la cuenta, revise su email para verificar']);


        } catch (\Throwable $th) {
            DB::rollBack(); // Revertir la transacción 
            return response()->json([
                'msg' => 'Algo salió mal no se pudo registrar, inténtelo de nuevo',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    //comprobar token de verificación
    public function confirmAccount(Request $request) {
        try {
            
            //Validar token enviado
            $rules = ['email_verification_token' => 'required|uuid'];
            $messages = ['required' => 'Se necesita enviar un token',
            'uuid' => 'El token no cumple con el formato'];

            $validator= Validator::make($request->all(), $rules , $messages);

            if ($validator->fails()) {
                return response()->json(['msg' => $validator->errors()->first()], 400);
            }

            //Buscar usuario por el token
            $user = User::where('email_verification_token', $request->email_verification_token)->first();
            if(!$user) {
                return response()->json(['msg' => 'El token de confimación no es válido'], 404);
            }

            //Acciones
            $user->email_verification_token = null;  //Borrar el token
            $user->email_verified_at = now(); //Establecer fecha
            $user->save();

            return response()->json(['msg' => 'Cuenta confirmada exitosamente'], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'msg' => 'No se pudo confirmar la cuenta inténtelo más tarde',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    //Consulta de administradores
    public static function findAdmin () {
        return User::where('role', 'admin')->get();
    }

}
