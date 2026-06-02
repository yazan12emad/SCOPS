<?php
namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Subscription;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    use ApiResponse;

    // Submit a review for a subscription (rating 1-10, comment optional)
    public function store(Request $request){
        // check subscription belongs to user
        $subscription = Subscription::where('id', $request->subscription_id)
            ->where('user_id', $request->user()->user_id)
            ->first();

        if(!$subscription){
            return $this->error('Subscription not found', 404);
        }

        // validate rating between 1-10
        if($request->rating < 1 || $request->rating > 10){
            return $this->error('Rating must be between 1 and 10', 422);
        }

        $review = Review::create([
            'user_id'         => $request->user()->user_id,
            'subscription_id' => $request->subscription_id,
            'service_id'      => $subscription->service_id,
            'rating'          => $request->rating,
            'comment'         => $request->comment ?? null,
        ]);

        return $this->success($review, 'Review submitted');
    }

    // Get all reviews for a specific service
    public function index($service_id){
        $reviews = Review::where('service_id', $service_id)
            ->with('user')
            ->get();
        return $this->success($reviews, 'Reviews fetched');
    }
}
