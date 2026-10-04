<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    use ApiResponse;

    /**
     * Store a new newsletter subscriber.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255|unique:subscribers,email',
        ], [
            'email.unique' => 'This email address is already subscribed to our newsletter.',
        ]);

        $subscriber = Subscriber::create([
            'email' => $validated['email'],
        ]);

        return $this->successResponse(
            $subscriber,
            'Thank you for subscribing to our newsletter!',
            201
        );
    }
}