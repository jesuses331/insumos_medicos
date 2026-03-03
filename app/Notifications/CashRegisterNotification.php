<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CashRegisterNotification extends Notification
{
    use Queueable;

    public $cashRegister;
    public $message;

    /**
     * Create a new notification instance.
     */
    public function __construct($cashRegister, $message)
    {
        $this->cashRegister = $cashRegister;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'cash_register_id' => $this->cashRegister->id,
            'sucursal_id' => $this->cashRegister->sucursal_id,
            'message' => $this->message,
            'type' => 'cash_register'
        ];
    }
}
