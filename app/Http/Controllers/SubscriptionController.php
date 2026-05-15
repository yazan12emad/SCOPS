<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

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

    // Cancel a subscription
    public function cancel($id){
        $subscription = Subscription::find($id);
        if(!$subscription){
            return $this->error('Subscription not found', 404);
        }
        $subscription->update(['status' => 'cancelled']);
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
}
