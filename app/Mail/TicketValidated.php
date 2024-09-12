<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Ticket;
use App\Models\Project;
class TicketValidated extends Mailable
{
    use Queueable, SerializesModels;
    public $ticket;
    public $project;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Ticket $ticket, Project $project)
    {
        $this->ticket = $ticket;
        $this->project = $project;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.ticket_validated')
            ->subject('[EBUILD] Ticket Validated')
            ->from('crm@e-build.tn', config("app.name"))
            ->with([
                'ticket' => $this->ticket,
                'project' => $this->project,
            ]);
    }
}
