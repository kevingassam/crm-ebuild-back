<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

use Dompdf\Dompdf;
class DevisPdf extends Mailable
{
    use Queueable, SerializesModels;

    public $devis;
    public $pdf;

    /**
     * Create a new message instance.
     *
     * @param BinaryFileResponse $pdf
     * @return void
     */
    public function __construct($devis, $pdf)
    {
        $this->devis = $devis;
        $this->pdf = $pdf;
    }
    /**
     * Build the message.
     *
     * @return $this
     */

    public function build()
    {
       

        return $this->view('emails.devis-pdf')
            ->subject('[EBUILD] Your Devis PDF')
            ->attachData($this->pdf->output(), 'devis.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
