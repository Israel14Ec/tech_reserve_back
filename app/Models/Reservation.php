<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $table="reservations";
    protected $primaryKey= "id";
    protected $fillable = [
        "reservation_date",
        "id_schedules_lab",
        "id_user",
        "type_reservation",
        "status"
    ];

    protected $hidden = [
        "created_at",
        "updated_at"
    ];

}
