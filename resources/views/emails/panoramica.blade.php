@component('mail::message')
{{-- Saludo personalizado --}}
@if($role === 'paciente')
# Hola {{ $panoramica->paciente->nombre }} 👋
@else
# Hola Doctor: {{ $panoramica->medico->nombre }} 👨‍⚕️
@endif

{{-- Panel con datos de la panorámica y paciente --}}
@component('mail::panel')
**Fecha:** {{ $panoramica->fecha }}
**Paciente:** {{ $panoramica->paciente->nombre }} {{ $panoramica->paciente->apellido }}
**Cédula:** {{ $panoramica->paciente->cedula }}
@if($role === 'medico')
**Correo del paciente:** {{ $panoramica->paciente->correo }}
@endif
@endcomponent

{{-- Mensaje específico --}}
@if($role === 'paciente')
Tu doctor ** {{ $panoramica->medico->nombre }}** ha subido una nueva panorámica para tu caso.
@else
Se ha agregado una nueva panorámica para tu paciente **{{ $panoramica->paciente->nombre }} {{ $panoramica->paciente->apellido }}**.
@endif

{{-- Botón para ver / descargar --}}
@component('mail::button', ['url' => url('storage/' . $panoramica->archivo)])
Ver Panorámica
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent
