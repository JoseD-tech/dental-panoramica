<?php

use Inertia\Inertia;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Panoramica;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\PanoramicaController;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('dashboard', function () {

        $numero_pacientes = Paciente::count();
        $numero_medicos = Medico::count();
        $numero_panoramicas = Panoramica::count();

        $panoramicas = Panoramica::with(['paciente:id,nombre,apellido,cedula,correo', 'medico:id,nombre']) // relaciones
            ->select('id', 'paciente_id', 'medico_id', 'fecha', 'notas', 'archivo')
            ->paginate(10);

        return Inertia::render('Dashboard', [
            'panoramicas' => $numero_panoramicas,
            'pacientes' => $numero_pacientes,
            'medicos' => $numero_medicos,
            'lista_panoramicas' => $panoramicas
        ]);
    })->name('dashboard');

    // Pacientes Routes
    Route::group(['prefix' => 'pacientes'], function () {

        Route::get('/', [PacienteController::class, "index"])->name('pacientes.index');
        Route::get('/create', [PacienteController::class, "create"])->name('pacientes.create');
        Route::post('/', [PacienteController::class, "store"])->name('pacientes.store');
        Route::delete('/{id}', [PacienteController::class, "delete"])->name('pacientes.delete');
        Route::get('/{id}', [PacienteController::class, "edit"])->name('pacientes.edit');
        Route::put('/{paciente}', [PacienteController::class, 'update'])->name('pacientes.update');
    });

    // Medicos Routes
    Route::group(['prefix' => 'medicos'], function () {

        Route::get('/', [MedicoController::class, "index"])->name('medicos.index');
        Route::get('/create', [MedicoController::class, "create"])->name('medicos.create');
        Route::post('/', [MedicoController::class, "store"])->name('medicos.store');
        Route::delete('/{id}', [MedicoController::class, "delete"])->name('medicos.delete');
        Route::get('/{id}', [MedicoController::class, "edit"])->name('medicos.edit');
        Route::put('/{paciente}', [MedicoController::class, 'update'])->name('medicos.update');
    });


    // Panoramicas Routes
    Route::group(['prefix' => 'panoramicas'], function () {

        Route::get('/', [PanoramicaController::class, "index"])->name('panoramicas.index');
        Route::get('/create', [PanoramicaController::class, "create"])->name('panoramicas.create');
        Route::post('/', [PanoramicaController::class, "store"])->name('panoramicas.store');
        Route::post('/{panoramica}/enviar', [PanoramicaController::class, 'enviarCorreo'])->name('panoramicas.enviar');
        Route::delete('/{id}', [PanoramicaController::class, "delete"])->name('panoramicas.delete');
        Route::get('/{id}', [PanoramicaController::class, "edit"])->name('panoramicas.edit');
        Route::put('/{paciente}', [PanoramicaController::class, 'update'])->name('panoramicas.update');
    });


    // Esto va a invocar enviarCorreo() con el panoramica cuyo ID pase en la URL
    Route::get('/test-mail/{panoramica}', [PanoramicaController::class, 'enviarCorreo'])
        ->where('panoramica', '[0-9]+');
});


require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
