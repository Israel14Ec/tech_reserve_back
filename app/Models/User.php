<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'users'; 
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'last_name',
        'cell_number',
        'email',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    //Relacion a n con la tabla intermedia reservations
    public function schedulesLabs() : BelongsToMany{
        return $this->belongsToMany(SchedulesLab::class, 'reservations', 'id_user', 'id_schedules_lab')
            ->withPivot('reservation_date', 'type_reservation', 'status')
            ->withTimestamps();
    }

}
