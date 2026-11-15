<?php

namespace App\Mail;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewAccountRegisteredMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
        public ?Tenant $tenant = null,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $storeName = $this->tenant?->name ?: 'Storefront';
        $planLabel = strtoupper((string) ($this->tenant?->plan ?: $this->user->role ?: 'customer'));

        return new Envelope(
            subject: "New ShopManager Sign Up: {$this->user->name} ({$storeName} • {$planLabel})",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.new-account-registered',
            with: [
                'whatsappReachoutUrl' => $this->buildWhatsappReachoutUrl(),
            ],
        );
    }

    /**
     * Build a clickable WhatsApp reach-out URL from the registered phone number.
     */
    private function buildWhatsappReachoutUrl(): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $this->user->phone);
        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0') && strlen($digits) === 11) {
            $digits = '234'.substr($digits, 1);
        }

        $storeName = $this->tenant?->name ?: 'ShopManager';
        $planName = $this->tenant?->plan ?: 'standard';
        $message = rawurlencode(
            "Hello {$this->user->name}, welcome to ShopManager! We saw your new store registration for {$storeName} ({$planName} plan) and are reaching out to help with billing and plan activation."
        );

        return "https://wa.me/{$digits}?text={$message}";
    }
}
