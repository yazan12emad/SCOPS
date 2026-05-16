<?php
namespace App\Console\Commands;
use App\Mail\RenewalReminderMail;
use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendRenewalReminders extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Send renewal reminder emails to users whose subscriptions are due soon';

    public function handle()
    {
        $subscriptions = Subscription::where('status', 'active')
            ->with(['user', 'service'])
            ->get();

        foreach ($subscriptions as $subscription) {
            $daysUntilRenewal = now()->diffInDays($subscription->renewal_date, false);

            if ($daysUntilRenewal == $subscription->reminder_days) {
                // Create database notification
                \App\Models\Notification::create([
                    'user_id' => $subscription->user->user_id,
                    'title'   => 'Subscription Renewal Reminder',
                    'message' => "Your {$subscription->service->name} subscription will renew in {$daysUntilRenewal} days.",
                    'is_read' => false,
                    'type'    => 'reminder',
                ]);

                // Send email notification
                Mail::to($subscription->user->email)->send(
                    new RenewalReminderMail(
                        $subscription->user->first_name,
                        $subscription->service->name,
                        $subscription->renewal_date,
                        $daysUntilRenewal
                    )
                );
            }
        }

        $this->info('Renewal reminders sent!');
    }
}
