<?php

namespace App\Mail;

use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The notification title, message and related data.
     */
    public $title;
    public $message;
    public $data;

    /**
     * Create a new message instance.
     * 
     * @param string $title The notification title
     * @param string $message The notification message
     * @param mixed $data Related data (game, order, etc)
     */
    public function __construct(string $title, string $message, $data = null)
    {
        $this->title = $title;
        $this->message = $message;
        $this->data = $data;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notification',
            with: [
                'title' => $this->title,
                'message' => $this->message,
                'data' => $this->data,
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
