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
    public $tipo;

    public function __construct($panoramica, $pathCompleto, $tipo)
    {
        $this->panoramica   = $panoramica;
        $this->pathCompleto = $pathCompleto;
        $this->tipo         = $tipo;      // 'paciente' o 'medico'
    }

    public function build()
    {
        return $this
            ->subject('Nueva panorámica disponible')
            ->markdown('emails.panoramica')
            ->attachFromStorageDisk('public', $this->pathCompleto);
    }
}
