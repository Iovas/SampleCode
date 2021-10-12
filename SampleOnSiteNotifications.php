<?php
namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Task extends Notification
{
    use Queueable;

    private $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function via($notifiable)
    {
       return ['database'];
   }

   public function toMail($notifiable)
   {

       return (new MailMessage)
       ->greeting($this->details['greeting'])
       ->line($this->details['title'])
       ->line($this->details['thanks']);

   }  

   public function toDatabase($notifiable)
   {
    return [
     'title' => $this->details['title'],
     'date' => $this->details['date'],
     'task_id' => $this->details['task_id'],
     'lead_id' => $this->details['lead_id']
 ];
}
}
