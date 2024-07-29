<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MeetCreated extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $idmeet;
protected $idguest;
protected $url;
protected $start;
protected $end;
protected $title;
protected $description;
    public function __construct($idMeet,$idguest,$meetUrl,$strattime,$endtime,$title,$description)
    {
        $this->idmeet=$idMeet;
            $this->idguest=$idguest;
            $this->url=$meetUrl;
            $this->start=$strattime;
            $this->end=$endtime;
            $this->title=$title;
            $this->description=$description;
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
    }public function toDatabase($notifiable)
    {
        return [
            'idMeet'=>$this->idmeet,
            'idguest'=>$this->idguest,
            'meetUrl'=>$this->url,
            'starttime'=>$this->start,
            'endtime'=>$this->end,
            'title'=>$this->title,
            'description'=>$this->description,
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
