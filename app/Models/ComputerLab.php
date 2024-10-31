<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComputerLab extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'computer_labs'; 
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'ability',
        'equipment',
        'location'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    

    // Relación n a schedules_labs
    public function schedulesLabs(): HasMany
    {
        return $this->hasMany(SchedulesLab::class, 'id_computer_labs');
    }

}
