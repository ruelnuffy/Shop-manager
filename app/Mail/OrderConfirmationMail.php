<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param  array{bank_name: string, account_number: string, account_name: string}|null  $bankAccount
     */
    public function __construct(
        public Order $order,
        public ?Tenant $tenant = null,
        public ?array $bankAccount = null,
        public string $whatsappConfirmUrl = '',
    ) {}

    public function envelope(): Envelope
    {
        $storeName = $this->tenant?->name ?? config('app.name', 'ShopManager');

        return new Envelope(
            subject: "Order Confirmation {$this->order->order_number} — {$storeName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-confirmation',
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
