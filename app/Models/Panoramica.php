<?php

namespace App\Models;

use App\Models\Medico;
use App\Models\Paciente;
use Illuminate\Database\Eloquent\Model;

class Panoramica extends Model
{
    protected $fillable = [
        'paciente_id',
        'medico_id',
        'notas',
        'archivo',
        'fecha',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class);
    }
}
