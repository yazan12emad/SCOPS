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
        $targetDate = now()->addDays(3)->toDateString();

        $subscriptions = Subscription::where('status', 'active')
            ->whereDate('renewal_date', $targetDate)
            ->with(['user', 'service'])
            ->get();

        foreach ($subscriptions as $subscription) {

            try {
                // DB notification
                \App\Models\Notification::create([
                    'user_id' => $subscription->user->user_id,
                    'title'   => 'Subscription Renewal Reminder',
                    'message' => "Your {$subscription->service->name} subscription will renew in 3 days.",
                    'is_read' => false,
                    'type'    => 'reminder',
                ]);

                // Email
                Mail::to($subscription->user->email)->send(
                    new RenewalReminderMail(
                        $subscription->user->first_name,
                        $subscription->service->name,
                        $subscription->renewal_date,
                        3
                    )
                );

            } catch (\Exception $e) {
                \Log::error('Renewal reminder failed: ' . $e->getMessage());
            }
        }

        $this->info('Renewal reminders sent!');
    }
}
