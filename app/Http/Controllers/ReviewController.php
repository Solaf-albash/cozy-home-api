<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * تخزين تقييم جديد لحجز مكتمل.
     */
    public function store(Request $request, Booking $booking)
    {
        if ($request->user()->id !== $booking->renter_id) {
            abort(403, 'You are not authorized to review this booking.');
        }

        if ($booking->status !== 'completed') {
            return response()->json([
                'message' => 'You can only review bookings after they have been completed.'
            ], 403); // Forbidden
        }

        if ($booking->review()->exists()) {
            return response()->json(['message' => 'You have already submitted a review for this booking.'], 409); // Conflict
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:500'],
        ]);

        $review = $booking->review()->create($validated);

        return response()->json([
            'message' => 'Thank you for your review!',
            'review' => $review
        ], 201);
    }
}
