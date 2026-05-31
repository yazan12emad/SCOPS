<?php
namespace App\Mail;

use Illuminate\Mail\Mailable;

class EmailVerificationMail extends Mailable
{
    public function __construct(
        public string $firstName,
        public string $verificationCode
    ) {}

    public function build()
    {
        return $this->subject('Verify Your SCOPS Email')
            ->view('emails.email-verification');
    }
}
