<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Appointment;

class AppointmentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Appointment $appointment, public string $recipientName) {}

    public function build(): self
    {
        $date   = $this->appointment->next_consultation?->format('F j, Y') ?? 'tomorrow';
        $doctor = $this->appointment->doctor ?: 'the clinic doctor';

        return $this
            ->subject('Reminder: Clinic Appointment Tomorrow')
            ->html("
                <h2>Hello {$this->recipientName}!</h2>
                <p>This is a reminder that you have a clinic appointment <strong>tomorrow ({$date})</strong>.</p>
                <p><strong>Doctor:</strong> {$doctor}</p>
                <p>Please arrive on time. If you need to cancel, log in to the portal.</p>
                <p style='font-size:12px;color:#666;'>This is an automated reminder. Please do not reply.</p>
            ");
    }
}
