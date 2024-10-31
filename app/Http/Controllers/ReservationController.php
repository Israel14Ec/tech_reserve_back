<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Reservation;
use App\Models\SchedulesLab;
use App\Models\ComputerLab;
use App\Http\Requests\ReservationStore;
Use App\Http\Controllers\UserController;
use App\Notifications\ReservationNotify;

class ReservationController extends Controller
{
    //crear reservación
    public function createReservation (ReservationStore $request) {
        try {
            
            $message = 'solicito la reserva del laboratorio';
            DB::beginTransaction();
            //Validar que la reserva ya no haya sido tomada en la misma fecha y laboratorio
            $existingReservation = Reservation::where('reservation_date', $request->reservation_date)
                ->where('id_schedules_lab', $request->id_schedules_lab)
                ->where('status', '!=', 'rejected') 
                ->exists(); 

            if($existingReservation) {
                return response()->json(
                    ['msg' => 'Ya existe una reservación para ese laboratorio en ese horario'], 400);
            }

            //Crear reservación
            $reservation = Reservation::create($request->all());
            $user = User::find($request->id_user); 
            $schedulesLab = SchedulesLab::with('computerLab')->find($request->id_schedules_lab);

            //Enviar notificación
            $userAdmin = UserController::findAdmin();
  
            foreach ($userAdmin as $admin) {
                $admin->notify(new ReservationNotify($user, $schedulesLab->computerLab, $schedulesLab, $message));
            }
            
            DB::commit();
            return response()->json([
                'msg' => 'Se realizó la reservación del laboratorio',
                'data' => $reservation
                ],201);
            
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'msg' => 'Error al crear la reserva', 
                'error' => $th->getMessage()], 500);
        }
    }      
    
    //Obtener reserva en función del usuario
    public function getReservationByUser (Request $request) {
        try {
            $user = $request->user();
            $per_page = $request->per_page;
            $current_page = $request->current_page;

            if($user->role == 'admin') {
                return response()->json([
                    'msg' => 'El administrador no puedo ver las reservas de horarios del usuario'
                ], 401);
            }

            $reservations = DB::table('reservations as re')
            ->join('schedules_labs as sc', 're.id_schedules_lab', '=', 'sc.id')
            ->join('computer_labs as co', 'sc.id_computer_labs', '=', 'co.id')
            ->select(
                're.id as id_reservation',
                're.reservation_date',
                're.type_reservation',
                're.status',
                'sc.start_time',
                'sc.end_time',
                'co.name as name_lab',
                'co.ability',
                'co.equipment',
                'co.location'
            )
            ->where('re.id_user', $user->id)
            ->paginate($per_page, ['*'],'page',$current_page);

            return response()->json($reservations, 200);

        } catch (\Throwable $th) {
            return response()->json([
                'msg' => 'No se pudo obtener las reservas, inténtelo más tarde', 
                'error' => $th->getMessage()], 500);
        }
    }

    //Estado de la reservación
    public function statusReservation (Request $request, $id) {
        try { 
            DB::beginTransaction();
            $rules = ['status' => 'required|in:requested,confirmed,rejected'];
            $messages = [
                'status.required' => 'El campo de estado es obligatorio.',
                'status.in' => 'El estado debe ser uno de los siguientes: requested, confirmed, rejected.'
            ];
            $messageEmail = [
                'confirmed' => 'se confirmó la reserva del laboratorio ',
                'rejected' => 'se rechazo la reserva del laboratorio '
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

             // Comprobar si hay errores
            if ($validator->fails()) {
                return response()->json([
                    'msg' => $validator->errors()->first() // Devolver el primer mensaje de error
                ], 400);
            }

            $reservation = Reservation::find($id);

            if(!$reservation) {
                return response()->json(['msg' => 'Reservación no encontrada'], 404);
            }
            $reservation->status = $request->status;
            $reservation->save();
            
            $user = User::find($reservation->id_user);
            $schedulesLab = SchedulesLab::with('computerLab')->find($reservation->id_schedules_lab);

            //mandar notificación al usuario
            if($reservation->status !== 'requested') {
                $user->notify(new ReservationNotify(
                    $user, 
                    $schedulesLab->computerLab, 
                    $schedulesLab, 
                    $messageEmail[$reservation->status])
                );
            }
            

            DB::commit();
            return response()->json(['msg' => 'Se cambió el estado de la reserva', 'data' =>$reservation], 201);
        } catch (\Throwable $th) {

            DB::rollBack();
            return response()->json([
                'msg' => 'No se pudo actualizar el estado, inténtelo más tarde', 
                'error' => $th->getMessage()], 500);
        }
    }

    //Modificar reserva
    public function update (ReservationStore $request, $id) {
        try {
            $reservation = Reservation::find($id);

            if(!$reservation){
                return response()->json(['msg' => 'No se encontró el laboratorio'] ,404);
            }

            $existingReservation = Reservation::where('reservation_date', $request->reservation_date)
            ->where('id_schedules_lab', $request->id_schedules_lab)
            ->where('status', '!=', 'rejected') // Excluir reservas rechazadas
            ->where('id', '!=', $id) // Excluir la reserva que se está modificando
            ->exists(); 

            $reservation->update($request->all());
            return response()->json(['msg' => 'Rservación modificada', 'data' =>$reservation]);

        } catch (\Throwable $th) {
            return response()->json([
                'msg' => 'No se pudo modificar la reservación, inténtelo más tarde', 
                'error' => $th->getMessage()], 500);
        }
    }

    //Eliminar reservación
    public function deleteReservation ($id) {
        try {
            DB::beginTransaction();

            $message = 'cancelo la reserva del laboratorio';
            $reservation = Reservation::find($id);
            $reservation->delete();

              //Enviar notificación
            $userAdmin = UserController::findAdmin();

            $user = User::find($reservation->id_user);
            $schedulesLab = SchedulesLab::with('computerLab')->find($reservation->id_schedules_lab);
  
            foreach ($userAdmin as $admin) {
                  $admin->notify(new ReservationNotify($user, $schedulesLab->computerLab, $schedulesLab, $message));
            }

            DB::commit();
            return response()->json(['msg' => 'Se cancelo la reservación', 'data'=>$reservation]);

        } catch (\Throwable $th) {

            DB::rollback();
            return response()->json([
                'msg' => 'Error al cancelar la reserva, inténtelo más tarde', 
                'error' => $th->getMessage()], 500);
        }
    }
}
