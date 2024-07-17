<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskCompleted extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $intituler;
    protected $name;
    protected $personnelname;
    protected $idproject;
    public function __construct($idproject,$personnelname,$name,$intituler)
    {
        $this->idproject=$idproject;
        $this->personnelname=$personnelname;
        $this->name=$name;
        $this->intituler=$intituler;
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
            'personnelname'=>$this->personnelname,
            'project_name'=>$this->name,
            'intituler'=>$this->intituler,
            
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
