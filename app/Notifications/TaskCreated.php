<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskCreated extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $idproject;
    protected $idpersonnel;
    protected $object;
    protected $name;
    protected $intituler;
    protected $idtache;
    public function __construct($idproject,$idpersonnel,$name,$object,$intituler,$idtache)
    {
        $this->idproject=$idproject;
        $this->idpersonnel=$idpersonnel;
        $this->name=$name;
        $this->object=$object;
        $this->intituler=$intituler;
        $this->idtache=$idtache;
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

    public function toDatabase($notifiable)
    {
        return [
            'idproject'=>$this->idproject,
            'idtache'=>$this->idtache,
            'idpersonnel'=>$this->idpersonnel,
            'project_name'=>$this->name,
            'intituler'=>$this->intituler,
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
