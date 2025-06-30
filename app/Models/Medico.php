<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medico extends Model
{

    public $fillable = [
        'nombre',
        'especialidad',
        'cedula',
        'telefono',
        'correo',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function panoramicas()
    {
        return $this->hasMany(Panoramica::class);
    }

}
