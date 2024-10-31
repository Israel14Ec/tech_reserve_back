<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SchedulesLab extends Model
{
    use HasFactory;

    protected $table='schedules_labs';
    protected $primaryKey='id';
    protected $fillable = [
        'start_time',
        'end_time',
        'is_availability',
        'id_computer_labs'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    //Relacion 1 a computer_labs
    public function computerLab () : BelongsTo {
        return $this->belongsTo(ComputerLab::class, 'id_computer_labs');
    }

    //Relacion n con users mediante la tabla intermedia reservations
    public function users():BelongsToMany {
        return $this->belongsToMany(User::class, 'reservations', 'id_schedules_lab', 'id_user')->withTimestamps();
    }
 
}
