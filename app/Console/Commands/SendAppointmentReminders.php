<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\Appointment;
use App\Models\User;
use App\Mail\AppointmentReminderMail;

class SendAppointmentReminders extends Command
{
    protected $signature   = 'clinichub:reminders';
    protected $description = 'Send email reminders for appointments scheduled tomorrow';

    public function handle(): void
    {
        $tomorrow = now()->addDay()->toDateString();

        $appointments = Appointment::where('status', 'Upcoming')
            ->whereDate('next_consultation', $tomorrow)
            ->where('reminder_1day_sent', false)
            ->get();

        $sent = 0;

        foreach ($appointments as $appt) {
            $student = User::where('id_number', $appt->student_id)->first();

            if (!$student || !$student->email) continue;

            try {
                Mail::to($student->email)->send(
                    new AppointmentReminderMail($appt, $student->first_name)
                );

                $appt->update(['reminder_1day_sent' => true]);
                $sent++;

                $this->info("Reminder sent to {$student->email} for appointment {$appt->appointment_id}");
            } catch (\Exception $e) {
                $this->error("Failed for {$student->email}: " . $e->getMessage());
            }
        }

        $this->info("Done. {$sent} reminder(s) sent.");
    }
}
