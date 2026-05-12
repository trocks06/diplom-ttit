<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentReminder extends Mailable
{
    use Queueable, SerializesModels;

    public Appointment $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Reminder about tomorrow's appointment"
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.appointment-reminder',
            with: [
                'appointment' => $this->appointment,
                'patientName' => $this->appointment->patient->user->firstname ?? 'пациент',
                'doctorName' => optional($this->appointment->schedule->doctor->user)->firstname ?? 'врач',
                'startTime' => $this->appointment->schedule->start_time->format('d.m.Y H:i'),
            ]
        );
    }
}
