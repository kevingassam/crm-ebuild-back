<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

use Dompdf\Dompdf;
class FacturePdf extends Mailable
{
    use Queueable, SerializesModels;

    public $facture;
    public $pdf;

    /**
     * Create a new message instance.
     *
     * @param BinaryFileResponse $pdf
     * @return void
     */
    public function __construct($facture, $pdf)
    {
        $this->facture = $facture;
        $this->pdf = $pdf;
    }
    /**
     * Build the message.
     *
     * @return $this
     */

    public function build()
    {
       

        return $this->view('emails.facture-pdf')
            ->subject('[EBUILD] Your Facture PDF')
            
            ->attachData($this->pdf->output(), 'facture.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
