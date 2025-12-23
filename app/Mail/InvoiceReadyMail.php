<?php

namespace App\Mail;

use App\Models\Order;
use App\Services\SignedUrlService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceReadyMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public string $invoiceUrl;
    public int $expiresInHours;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, int $expiresInMinutes = 60)
    {
        $this->order = $order;
        $this->expiresInHours = (int)ceil($expiresInMinutes / 60);
        
        // Generate temporary signed URL
        $this->invoiceUrl = SignedUrlService::generateInvoiceUrl($order->id, $expiresInMinutes);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your Invoice is Ready - Order #{$this->order->id}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice-ready',
            with: [
                'order' => $this->order,
                'invoiceUrl' => $this->invoiceUrl,
                'expiresInHours' => $this->expiresInHours,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
