<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransferNotification extends Notification
{
    use Queueable;

    public $transfer;
    public $message;

    /**
     * Create a new notification instance.
     */
    public function __construct($transfer, $message)
    {
        $this->transfer = $transfer;
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
            'transfer_id' => $this->transfer->id,
            'from_branch_id' => $this->transfer->from_branch_id,
            'to_branch_id' => $this->transfer->to_branch_id,
            'from_branch_name' => $this->transfer->fromSucursal->nombre ?? 'Otra Sucursal',
            'message' => $this->message,
            'type' => 'transfer'
        ];
    }
}
