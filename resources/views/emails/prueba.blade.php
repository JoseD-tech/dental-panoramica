<x-mail::message>
# Introduction

The body of your message.

### Prubas desde **practicas**

{{
    $pacientes
}}

<x-mail::button :url="''">
Button
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
