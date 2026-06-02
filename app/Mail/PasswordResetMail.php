<?php
namespace App\Mail;
use Illuminate\Mail\Mailable;

class PasswordResetMail extends Mailable
{
    public function __construct(
        public string $firstName,
        public string $resetCode
    ) {}

    public function build()
    {
        return $this->subject('Reset Your SCOPS Password')
            ->view('emails.password-reset');
    }
}
