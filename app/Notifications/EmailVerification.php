<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerification extends Notification
{
    use Queueable;

    protected $user;
    protected $token;

    /**
     * Create a new notification instance.
     */
    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {

        $url = env('FRONTEND_URL').'/confirmar_cuenta/'.$this->token;
        return (new MailMessage)
                    ->greeting('Hola ' . $this->user->name . ' '.$this->user->last_name)
                    ->subject('Creación de cuenta')
                    ->line('Creaste una nueva cuenta en Tech Reserve, confirmala')
                    ->action('Confirmar', $url)
                    ->line('Gracias por usar la aplicación!');
    }
   
}
