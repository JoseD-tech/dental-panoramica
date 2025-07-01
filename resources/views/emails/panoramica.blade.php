@component('mail::message')
# Panorámica Adjunta

@if ($tipo === 'paciente')
Hola {{ $panoramica->paciente->nombre ?? 'Paciente' }},
Se ha registrado una nueva panorámica a tu nombre.
@else
Hola {{ $panoramica->medico->nombre ?? 'Doctor(a)' }},
Un nuevo paciente ha sido asignado con una panorámica.
@endif

**Datos del paciente:**

- Nombre: {{ $panoramica->paciente->nombre ?? '-' }}
- Cédula: {{ $panoramica->paciente->cedula ?? '-' }}
- Fecha: {{ $panoramica->fecha }}
- Notas: {{ $panoramica->notas }}

**Detalles de la panorámica:**
@component('mail::button', ['url' => asset('storage/' . $panoramica->archivo)])
Ver Panorámica Original (PDF)
@endcomponent
@component('mail::button', ['url' => asset('storage/' . $pathCompleto)])
Haz clic aquí para ver la imagen (JPG)
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent
