<?php

use App\Models\Medico;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Buscar pacientes por cédula
Route::get('/pacientes', function (Request $request) {
    $cedula = $request->query('cedula');
    return Paciente::where('cedula', 'like', "%{$cedula}%")
        ->select('id', 'nombre', 'apellido', 'cedula')
        ->limit(10)
        ->get();
});

// Buscar médicos por nombre
Route::get('/medicos', function (Request $request) {
    $nombre = $request->query('nombre');
    return Medico::where('nombre', 'like', "%{$nombre}%")
        ->select('id', 'nombre')
        ->limit(10)
        ->get();
});
