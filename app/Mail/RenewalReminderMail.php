<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RenewalReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $firstName;
    public string $serviceName;
    public string $renewalDate;
    public int $daysLeft;

    public function __construct(string $firstName, string $serviceName, string $renewalDate, int $daysLeft)
    {
        $this->firstName   = $firstName;
        $this->serviceName = $serviceName;
        $this->renewalDate = $renewalDate;
        $this->daysLeft    = $daysLeft;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Subscription Renewal Reminder');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.renewal-reminder');
    }
}
