<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RenewalEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $productDetails;

    /**
     * Create a new message instance.
     */
    public function __construct($data , $productDetails)
    {
        $this->data = $data;
        $this->productDetails = $productDetails;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Service Renewal Reminder',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.renewal',
        );
    }

    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'emails.renewal',
    //         with: ['data' => $this->data, 'productDetails' => $this->productDetails]
    //     );
    // }

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
