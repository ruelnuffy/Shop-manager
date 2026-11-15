<?php

namespace App\Mail;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionExpiryNoticeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Tenant $tenant,
        public string $noticeType = 'expiring_soon',
        public int $daysRemaining = 0,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $planLabel = strtoupper((string) ($this->tenant->plan ?: 'standard'));
        $subject = $this->noticeType === 'expired'
            ? "ShopManager Subscription Expired: {$this->tenant->name} ({$planLabel})"
            : "Heads Up: {$this->tenant->name} ({$planLabel}) expires in {$this->daysRemaining} day(s)";

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $whatsappMessage = rawurlencode(
            "Hello ShopManager! I would like to renew my {$this->tenant->plan} plan for \"{$this->tenant->name}\"."
        );

        return new Content(
            view: 'emails.subscription-expiry-notice',
            with: [
                'whatsappRenewUrl' => "https://wa.me/2349046686176?text={$whatsappMessage}",
            ],
        );
    }
}
