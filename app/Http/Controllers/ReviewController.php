<?php
namespace App\Http\Controllers;
use App\Models\Review;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    use ApiResponse;

    public function store(Request $request){
        $review = Review::create([
            'user_id'         => $request->user()->user_id,
            'service_id'      => $request->service_id,
            'subscription_id' => $request->subscription_id,
            'rating'          => $request->rating,
            'comment'         => $request->comment,
        ]);
        return $this->success($review, 'Review submitted');
    }

    public function index($service_id){
        $reviews = Review::where('service_id', $service_id)->get();
        return $this->success($reviews, 'Reviews fetched');
    }
}
