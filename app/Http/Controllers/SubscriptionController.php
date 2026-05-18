<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Service;                          
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubscriptionCancelledMail;

class SubscriptionController extends Controller
{
    use ApiResponse;

    // Get all subscriptions for the logged in user
    public function index(Request $request){
        $query = Subscription::where('user_id', $request->user()->user_id);

        // filter by status (active,cancelled,paused)
        if($request->has('status')){
            $query->where('status', $request->status);
        }


        // filter by category
        if($request->has('category_id')){
            $query->whereHas('service', function($q) use ($request){
                $q->where('category_id', $request->category_id);
            });
        }

        // sort by renewal date to see which subscription renew soon
        if($request->has('sort_order')){
            $direction = $request->sort_order === 'desc' ? 'desc' : 'asc';
            $query->orderBy('renewal_date', $direction);
        }

        // search by service name
        if($request->has('search')){
            $query->whereHas('service', function($q) use ($request){
                $q->where('name', 'like', '%'.$request->search.'%');
            });
        }

        return $this->success($query->get(), 'Subscriptions fetched');
    }

    // Create a new subscription
    // Request $request mean that larval gives you the http request automatically
    public function store(Request $request){
        $subscription = Subscription::create([
            'user_id'      => $request->user()->user_id,
            'service_id'   => $request->service_id,
            'card_id'      => $request->card_id,
            'plan_id'      => $request->plan_id,
            'amount'       => $request->amount,
            'billing_cycle'=> $request->billing_cycle,
            'start_date'   => $request->start_date,
            'renewal_date' => $request->renewal_date,
            'status'       => 'active',
            'reminder_days' => $request->reminder_days,
            'priority'     => $request->priority,
        ]);
        return $this->success($subscription, 'Subscription created');
    }

    // Pause a subscription
    public function pause($id){
        $subscription = Subscription::find($id);
        if(!$subscription){
            return $this->error('Subscription not found', 404);
        }
        $subscription->update(['status' => 'paused']);
        return $this->success($subscription, 'Subscription paused');
    }

    // Resume a subscription
    public function resume($id){
        $subscription = Subscription::find($id);
        if(!$subscription){
            return $this->error('Subscription not found', 404);
        }
        $subscription->update(['status' => 'active']);
        return $this->success($subscription, 'Subscription resumed');
    }

    //Cancel a subscription
    public function cancel($id){
        $subscription = Subscription::find($id);
        if(!$subscription){
            return $this->error('Subscription not found', 404);
        }
        $subscription->update(['status' => 'cancelled']);

        // Send cancellation email in background (queued)
        $service = Service::find($subscription->service_id);
        Mail::to($subscription->user->email)->queue(     // ← CHANGED to queue
            new SubscriptionCancelledMail($subscription->user->first_name, $service->name)
        );

        return $this->success($subscription, 'Subscription cancelled');
    }

    // Calculate next renewal date
    public function calculateNextRenewal($id){
        $subscription = Subscription::find($id);
        if(!$subscription){
            return $this->error('Subscription not found', 404);
        }
        $renewal = match($subscription->billing_cycle){
            'weekly'  => now()->addWeek(),
            'monthly' => now()->addMonth(),
            'yearly'  => now()->addYear(),
        };
        $subscription->update(['renewal_date' => $renewal]);
        return $this->success($subscription, 'Renewal date updated');
    }

    //returns spending data for charts
    public function financialSummary(Request $request){
        $userId = $request->user()->user_id;

        $subscriptions = Subscription::where('user_id', $userId)
            ->where('status', 'active')
            ->get();

        $monthlyTotal = $subscriptions
            ->where('billing_cycle', 'monthly')
            ->sum('amount');

        $yearlyTotal = $subscriptions
            ->where('billing_cycle', 'yearly')
            ->sum('amount');

        $weeklyTotal = $subscriptions
            ->where('billing_cycle', 'weekly')
            ->sum('amount');

        return $this->success([
            'total_monthly_spending'  => round($monthlyTotal, 2),// to keep 2 numbers after the decimal
            'total_yearly_spending'   => round($yearlyTotal, 2),
            'total_weekly_spending'   => round($weeklyTotal, 2),
            'estimated_monthly_cost'  => round($monthlyTotal + ($yearlyTotal / 12) + ($weeklyTotal * 4), 2),
            'active_subscriptions'    => $subscriptions->count(),
        ], 'Financial summary fetched');
    }
}
