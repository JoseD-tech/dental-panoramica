<?php

namespace App\Http\Controllers;

use App\Models\Medico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MedicoController extends Controller
{

    public function index()
    {

        $medicos = DB::table('medicos')
            ->select('id', 'nombre', 'especialidad', "telefono", 'cedula', 'correo')
            ->paginate(10); // 10 por página
        ;

        return inertia("Medicos/Index", [
            'medicos' => $medicos,
        ]);
    }

    public function create()
    {
        // Logic to show the form for creating a new medico
        return inertia('Medicos/Create');
    }

    public function store(Request $request)
    {

        // Validación de los datos del formulario
        $request->validate([
            'nombre' => 'required|string|max:255',
            'especialidad' => 'required|string|max:255',
            'cedula' => 'required|string|min:7|max:10|regex:/^\d+$/',
            'telefono' => 'required|string|min:7|max:10|regex:/^\d+$/',
            'correo' => 'required|string|email|max:255',
        ]);

        Medico::create($request->all());

        return redirect()->route('medicos.index')->with('success', 'Medico registrado exitosamente.');
    }

    public function edit($medico)
    {
        $medico = DB::table('medicos')
            ->select('id', 'nombre', 'especialidad', 'cedula', 'telefono', 'correo')
            ->where('id', $medico)
            ->first();

        return inertia('Medicos/Edit', ['medico' => $medico]);
    }

    public function update(Request $request, $id)
    {

        $medico = Medico::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'especialidad' => 'required|string|max:255',
            'cedula' => 'required|string|unique:medicos,cedula,' . $medico->id,
            'telefono' => 'nullable|string',
            'correo' => 'required|email',
        ]);

        $medico->update($validated);

        return redirect()->route('medicos.index')->with('success', 'medico actualizado con éxito');
    }

    public function delete(Request $request)
    {

        $medico = Medico::findOrFail($request->id);
        $medico->delete();

        return redirect()->route('medicos.index')->with('success', 'Medico eliminado exitosamente.');
    }
}
