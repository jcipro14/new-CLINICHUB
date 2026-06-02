<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Appointment;
use App\Models\User;

class AppointmentConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Appointment $appointment,
        public User $student
    ) {}

    public function build(): self
    {
        $date   = $this->appointment->next_consultation?->format('F j, Y') ?? 'TBA';
        $doctor = $this->appointment->doctor ?: 'TBA';
        $reason = $this->appointment->reason ?: 'General consultation';

        return $this
            ->subject('Your Clinic Appointment is Confirmed')
            ->html("
                <h2>Hello {$this->student->first_name}!</h2>
                <p>Your appointment at the <strong>UM Visayan Clinic</strong> has been scheduled.</p>
                <table style='border-collapse:collapse;'>
                    <tr><td style='padding:4px 12px 4px 0;'><strong>Date:</strong></td><td>{$date}</td></tr>
                    <tr><td style='padding:4px 12px 4px 0;'><strong>Doctor:</strong></td><td>{$doctor}</td></tr>
                    <tr><td style='padding:4px 12px 4px 0;'><strong>Reason:</strong></td><td>{$reason}</td></tr>
                </table>
                <p>Please log in to the portal to confirm or cancel your appointment.</p>
                <p style='font-size:12px;color:#666;'>This is an automated message. Please do not reply.</p>
            ");
    }
}
