<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PanoramicaEnviada extends Mailable
{
    use Queueable, SerializesModels;

    public $panoramica;
    public $pathCompleto;
    public $role;

    public function __construct($panoramica, $pathCompleto, $role)
    {
        $this->panoramica   = $panoramica;
        $this->pathCompleto = $pathCompleto;
        $this->role         = $role;      // 'paciente' o 'medico'
    }

    public function build()
    {
        return $this
            ->subject('Nueva panorámica disponible')
            ->markdown('emails.panoramica')   // volvemos a Markdown
            ->attachFromStorageDisk('public', $this->pathCompleto);
    }
}
