<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function build(): self
    {
        return $this
            ->subject('Welcome to UM Visayan Clinic Portal')
            ->html("
                <h2>Hello {$this->user->first_name}!</h2>
                <p>Your account for the <strong>UM Visayan Clinic</strong> portal has been created.</p>
                <p><strong>ID Number:</strong> {$this->user->id_number}</p>
                <p>You can now log in using your ID number and password.</p>
                <p style='font-size:12px;color:#666;'>This is an automated message. Please do not reply.</p>
            ");
    }
}
