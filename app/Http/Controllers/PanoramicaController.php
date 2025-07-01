<?php

namespace App\Http\Controllers;

use App\Models\Panoramica;
use Illuminate\Http\Request;
use Spatie\PdfToImage\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\PanoramicaEnviada;
use Illuminate\Support\Facades\Http;

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

        $rutaPdf = storage_path('app/public/' . $panoramica->archivo);
        $nombreBase = pathinfo($panoramica->archivo, PATHINFO_FILENAME);
        $nombreImagen = $nombreBase . '.jpg';
        $rutaImagenPublic = 'panoramicas/' . $nombreImagen;
        $rutaImagenCompleta = storage_path('app/public/' . $rutaImagenPublic);

        try {
            // 1. Subir el archivo a PDF.co
            $apiKey = 'koguro123@gmail.com_nBzocsAzIrfA1kUVA5AafJWSzuV0g5wxNyk3ouI52gMrZvOOn2s9FUxULZVJmTrx'; // Usa el .env para seguridad

            $fileContent = file_get_contents($rutaPdf);
            $base64File = base64_encode($fileContent);

            $uploadResponse = Http::withHeaders([
                'x-api-key' => $apiKey,
            ])->post('https://api.pdf.co/v1/file/upload/base64', [
                'name' => basename($rutaPdf),
                'file' => $base64File,
            ]);

            if (!$uploadResponse->successful() || empty($uploadResponse['url'])) {
                return back()->with('error', 'Error al subir el PDF: ' . $uploadResponse->body());
            }

            $pdfUrl = $uploadResponse['url'];

            // 2. Convertir PDF a imagen
            $convertResponse = Http::withHeaders([
                'x-api-key' => $apiKey,
            ])->post('https://api.pdf.co/v1/pdf/convert/to/jpg', [
                'url' => $pdfUrl,
                'pages' => '0', // primera página
            ]);

            if (!$convertResponse->successful() || empty($convertResponse['urls'])) {
                return back()->with('error', 'Error en la conversión a imagen: ' . $convertResponse->body());
            }
            $imagenUrl = $convertResponse['urls'];
            $contenidoImagen = file_get_contents($imagenUrl[0]);

            // 3. Guardar la imagen en disco
            Storage::disk('public')->put($rutaImagenPublic, $contenidoImagen);

            if (!Storage::disk('public')->exists($rutaImagenPublic)) {
                return back()->with('error', 'La imagen no se pudo guardar correctamente.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error al procesar el PDF: ' . $e->getMessage());
        }

        // 4. Enviar correos
        if ($panoramica->paciente?->correo) {
            Mail::to($panoramica->paciente->correo)
                ->send(new PanoramicaEnviada($panoramica, $rutaImagenPublic, 'paciente'));
        }

        if ($panoramica->medico?->correo) {
            Mail::to($panoramica->medico->correo)
                ->send(new PanoramicaEnviada($panoramica, $rutaImagenPublic, 'medico'));
        }

        return back()->with('success', 'Correos enviados con imagen.');
    }
}
