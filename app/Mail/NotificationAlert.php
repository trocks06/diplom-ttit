<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificationAlert extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $text;

    public function __construct(User $user, string $text)
    {
        $this->user = $user;
        $this->text = $text;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New notice from the dental clinic'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.notification-alert',
            with: [
                'userName' => $this->user->firstname ?? 'пациент',
                'text'     => $this->text,
            ]
        );
    }
}
