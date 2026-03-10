<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AutomationTriggered extends Notification
{
    use Queueable;

    public function __construct(
        public string $message,
        public $lead = null
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'message' => $this->message,
            'lead_id' => $this->lead?->id,
            'lead_name' => $this->lead?->name,
            'type' => 'automation',
        ];
    }
}
