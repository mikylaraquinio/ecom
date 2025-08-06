<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'order_item_id' => 'required|exists:order_items,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string',
            'photo' => 'nullable|image|max:2048',
            'video' => 'nullable|mimetypes:video/mp4,video/avi,video/mpeg|max:10000',
        ]);

        $review = new Review();
        $review->product_id = $request->product_id;
        $review->order_item_id = $request->order_item_id;
        $review->user_id = auth()->id();
        $review->rating = $request->rating;
        $review->review = $request->review;
        $review->show_username = $request->has('show_username');

        if ($request->hasFile('photo')) {
            $review->photo_path = $request->file('photo')->store('reviews/photos', 'public');
        }

        if ($request->hasFile('video')) {
            $review->video_path = $request->file('video')->store('reviews/videos', 'public');
        }

        $review->save();

        return back()->with('success', 'Thank you for your review!');
    }

}

