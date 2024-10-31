<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\AuthStore;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AuthController extends Controller
{
    //Iniciar sesión
    public function loggin (AuthStore $request) {
        try {


           //Autenticamos las credenciales del usuario
            if (Auth::attempt($request->only('email', 'password'))) {
                $user = Auth::user(); //Obtiene al usuario para generar el token
                if($user && !$user->email_verified_at) {
                    return response()->json(['msg' => 'Aun no esta autenticado, revise su correo'], 402);   
                }
                $token = $user->createToken(
                    'auth_token', 
                    ['*'], 
                    Carbon::now()->addMonths(3) // Define la expiración a 3 meses    
                )->plainTextToken; //Creamos el token
                return response()->json(['auth_token' => $token, 'data' => $user], 200); //Devolvemos el token

            } 
            return response()->json(['msg' => 'Usuario o contraseña incorrectas, intentelo de nuevo'], 401); 
            
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => 'Algo salió mal no se pudo iniciar sesión',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    //Obtener perfil del token 
    public function userProfile(Request $request) {
        try {
            return response()->json([
                "data" => $request->user() //Obtiene al usuario autenticado, con auth()-user() es una funcion de Auth
            ], 200);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(),
            'msg' => 'Algo salió mal vuelva a intentarlo'], 500);
        }
    }

    //Cierre de sesión
    public function logout(Request $request) {
        try {
    
            $request->user()->tokens()->delete(); //Elimina todos los tokens
            return response()->json(['msg' => 'Sesión cerrada exitosamente'], 200);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(),
            'msg' => 'Algo salió mal vuelva a intentarlo'], 500);
        }
    }
}
