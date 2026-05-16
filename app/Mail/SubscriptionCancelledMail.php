<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionCancelledMail extends Mailable
{
    use Queueable, SerializesModels;
    //Queueable : allow the email to be sent in the background
    //SerializesModels : it converts objects to text and back again for the queue

    public string $firstName;
    public string $serviceName;

    public function __construct(string $firstName, string $serviceName)
    {
        $this->firstName   = $firstName;
        $this->serviceName = $serviceName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Subscription Cancelled');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.subscription-cancelled');
    }
}
