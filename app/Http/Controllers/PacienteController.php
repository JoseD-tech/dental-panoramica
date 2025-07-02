<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PacienteController extends Controller
{

    public function index()
    {

        $pacientes = DB::table('pacientes')
            ->select('id', 'nombre', 'apellido', "telefono", 'cedula', 'correo')
            ->paginate(10); // 10 por página
        ;

        return inertia('Pacientes/Index', [
            'pacientes' => $pacientes,
        ]);
    }

    public function create()
    {
        return inertia('Pacientes/Create');
    }

    public function store(Request $request)
    {
        // Validación de los datos del formulario
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cedula' => 'required|string|min:7|max:10|regex:/^\d+$/',
            'fecha_nacimiento' => 'required|date',
            'telefono' => 'required|string|min:7|max:10|regex:/^\d+$/',
            'correo' => 'required|string|email|max:255',
            'direccion' => 'required|string|max:255|unique:pacientes,cedula',
        ]);

        Paciente::create($request->all());

        return redirect()->route('pacientes.index')->with('success', 'Paciente creado exitosamente.');
    }

    public function edit($paciente)
    {
        $paciente = DB::table('pacientes')
            ->select('id', 'nombre', 'apellido', 'cedula', 'fecha_nacimiento', 'telefono', 'correo', 'direccion')
            ->where('id', $paciente)
            ->first();

        return inertia('Pacientes/Edit', ['paciente' => $paciente]);
    }

    public function update(Request $request, $id)
    {

        $paciente = Paciente::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cedula' => 'required|string|unique:pacientes,cedula,' . $paciente->id,
            'fecha_nacimiento' => 'nullable|date',
            'telefono' => 'nullable|string',
            'correo' => 'required|email',
            'direccion' => 'nullable|string',
        ]);

        $paciente->update($validated);

        return redirect()->route('pacientes.index')->with('success', 'Paciente actualizado con éxito');
    }

    public function delete(Request $request)
    {

        $paciente = Paciente::findOrFail($request->id);
        $paciente->delete();

        return redirect()->route('pacientes.index')->with('success', 'Paciente eliminado exitosamente.');
    }
}
