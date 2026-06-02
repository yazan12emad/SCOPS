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

        // DEBUG - remove after testing
        \Log::info('Reminder command running. Target date: ' . $targetDate);
        \Log::info('Current time: ' . now());

        $subscriptions = Subscription::where('status', 'active')
            ->whereDate('renewal_date', $targetDate)
            ->with(['user', 'service'])
            ->get();

        // DEBUG
        \Log::info('Subscriptions found: ' . $subscriptions->count());

        if ($subscriptions->isEmpty()) {
            $this->info('No renewals due in 3 days. Nothing sent.');
            return;
        }

        $sent = 0;
        $failed = 0;

        foreach ($subscriptions as $subscription) {
            try {
                \App\Models\Notification::create([
                    'user_id' => $subscription->user->user_id,
                    'title'   => 'Subscription Renewal Reminder',
                    'message' => "Your {$subscription->service->name} subscription will renew in 3 days.",
                    'is_read' => false,
                    'type'    => 'reminder',
                ]);

                Mail::to($subscription->user->email)->send(
                    new RenewalReminderMail(
                        $subscription->user->first_name,
                        $subscription->service->name,
                        $subscription->renewal_date,
                        3
                    )
                );

                $sent++;
            } catch (\Exception $e) {
                $failed++;
                \Log::error("Renewal reminder failed for subscription #{$subscription->id}: " . $e->getMessage());
            }
        }

        $this->info("Done. Sent: {$sent}, Failed: {$failed}");
    }
}
