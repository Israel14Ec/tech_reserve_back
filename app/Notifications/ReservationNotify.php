<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationNotify extends Notification
{
    use Queueable;

    protected $user;
    protected $computerLab;
    protected $schedule;
    protected $message;

    /**
     * Create a new notification instance.
     */
    public function __construct($user, $computerLab, $schedule, $message)
    {
        $this->user = $user;
        $this->computerLab = $computerLab;
        $this->schedule = $schedule;
        $this->message = $message;
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
        return (new MailMessage)
                    ->greeting('Solucitud de laboratorio')
                    ->subject('Notificación de solicitud de reserva')
                    ->line('El usuario **' . $this->user->name . ' ' . $this->user->last_name . '** '
                        .$this->message. '**' . $this->computerLab->name . '** ' .
                        'en el horario de: **' . $this->schedule->start_time . '** a **' . $this->schedule->end_time . '**'
                    )
                    ->line('Gracias por usar la aplicación!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
