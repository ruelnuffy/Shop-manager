<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StoreActivityNotification extends Notification
{
    use Queueable;

    /**
     * Create a new store activity notification instance.
     *
     * @param  array<string, mixed>  $meta
     */
    public function __construct(
        public string $eventType,
        public string $title,
        public string $message,
        public string $actionUrl,
        public string $actionLabel = 'Open Console',
        public array $meta = [],
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $storeName = (string) ($this->meta['store_name'] ?? config('app.platform_name', 'ShopManager'));

        $mail = (new MailMessage)
            ->subject("[{$storeName}] {$this->title}")
            ->greeting("Hello {$notifiable->name},")
            ->line($this->message);

        if (! empty($this->meta['summary'])) {
            $mail->line((string) $this->meta['summary']);
        }

        return $mail
            ->action($this->actionLabel, $this->actionUrl)
            ->line("Thank you for using {$storeName} on ".config('app.platform_name', 'ShopManager').'.');
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'event_type' => $this->eventType,
            'title' => $this->title,
            'message' => $this->message,
            'action_url' => $this->actionUrl,
            'action_label' => $this->actionLabel,
            'meta' => $this->meta,
        ];
    }
}
