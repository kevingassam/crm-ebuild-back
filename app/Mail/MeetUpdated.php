<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Meet;
class MeetUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $meet;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Meet $meet)
    {
        $this->meet=$meet;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.meet-updated')
            ->subject('[EBUILD] New Meet Updated');
    }
}
