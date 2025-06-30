<?php

namespace App\Http\Controllers;

use App\Models\Panoramica;
use Illuminate\Http\Request;
use App\Mail\PanoramicaEnviada;
use App\Models\Medico;
use App\Models\Paciente;
use Illuminate\Support\Facades\Mail;

class PanoramicaController extends Controller
{

    public function index()
    {

        $panoramicas = Panoramica::with(['paciente:id,nombre,apellido,cedula,correo', 'medico:id,nombre']) // relaciones
            ->select('id', 'paciente_id', 'medico_id', 'fecha', 'notas', 'archivo')
            ->paginate(10);

        return inertia('Panoramicas/Index', [
            'panoramicas' => $panoramicas,
        ]);
    }

    public function create()
    {
        return inertia('Panoramicas/Create');
    }

    public function store(Request $request)
    {
        // 1) Valida los campos
        $validated = $request->validate([
            'paciente_id' => 'required|string|max:255',
            'medico_id'   => 'required|string|max:255',
            'notas'        => 'required|string|max:1000',
            'archivo'     => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // 2) Guarda el archivo en disco (storage/app/public/panoramicas)
        //    y devuelve algo como "panoramicas/nombreArchivo.ext"
        $path = $request->file('archivo')->store('panoramicas', 'public');

        // 3) Sustituye el valor 'archivo' en el array validado
        $validated['archivo'] = $path;

        // 4) Crea la Panoramica con la ruta
        Panoramica::create($validated);

        // 5) Redirige con mensaje
        return redirect()
            ->route('panoramicas.index')
            ->with('success', 'Panorámica creada exitosamente.');
    }


    public function edit($panoramica)
    {
        $panoramica = Panoramica::with(['paciente', 'medico'])->findOrFail($panoramica);

        return inertia('Panoramicas/Edit', [
            'panoramica' => $panoramica,
        ]);
    }

    public function update(Request $request, $id)
    {

        $panoramica = Panoramica::findOrFail($id);

        $validated = $request->validate([
            'paciente_id' => 'required|string|max:255',
            'medico_id' => 'required|string|max:255',
            'notas' => 'required|string|max:1000',
            'archivos' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048', // Validación del archivo
            'fecha' => 'required|date',
        ]);

        $panoramica->update($validated);

        return redirect()->route('panoramicas.index')->with('success', 'Panoramicas actualizado con éxito');
    }

    public function delete(Request $request)
    {

        $panoramica = Panoramica::findOrFail($request->id);
        $panoramica->delete();

        return redirect()->route('panoramicas.index')->with('success', 'Panoramica eliminado exitosamente.');
    }

    public function enviarCorreo(Panoramica $panoramica)
    {
        $panoramica->load('paciente', 'medico');
        $ruta = $panoramica->archivo;

        // Al paciente
        if ($panoramica->paciente->correo) {
            Mail::to($panoramica->paciente->correo)
                ->send(new PanoramicaEnviada($panoramica, $ruta, 'paciente'));
        }

        // Al médico
        if ($panoramica->medico && $panoramica->medico->correo) {
            Mail::to($panoramica->medico->correo)
                ->send(new PanoramicaEnviada($panoramica, $ruta, 'medico'));
        }

        return back()->with('success', 'Correos enviados exitosamente');
    }
}
