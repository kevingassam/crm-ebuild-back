<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketValidated extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $idclient;
    protected $idticket;
    protected $object;
    protected $name;
    protected $project_name;
    public function __construct($name,$idclient,$idTicket,$object,$project_name)
    {
        $this->idclient=$idclient;
        $this->idticket=$idTicket;
        $this->name=$name;
        $this->object=$object;
        $this->project_name=$project_name;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }*/
    


/**
 * Get the array representation of the notification.
 *
 * @param  mixed  $notifiable
 * @return array
 */
public function toDatabase($notifiable)
{
    return [
        'idClient'=>$this->idclient,
        'idTicket'=>$this->idticket,
        'project_name'=>$this->project_name,
        'client_name'=>$this->name,
        'object'=>$this->object,
    ];
}

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
