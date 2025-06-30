<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{

    protected $fillable = [
        'nombre',
        'apellido',
        "cedula",
        'fecha_nacimiento',
        'telefono',
        'correo',
        'direccion',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    public function panoramicas()
    {
        return $this->hasMany(Panoramica::class);
    }

}
