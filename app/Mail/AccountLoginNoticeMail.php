<?php

namespace App\Mail;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountLoginNoticeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
        public ?Tenant $tenant = null,
        public ?string $ipAddress = null,
        public string $loggedInAt = '',
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $storeLabel = $this->tenant?->name ?: 'Platform';

        return new Envelope(
            subject: "ShopManager Login Notice: {$this->user->name} ({$storeLabel})",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.account-login-notice',
        );
    }
}
