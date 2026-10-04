<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Notifications\SendContactAdminNotification;
use Illuminate\Support\Facades\Notification;


class ContactController extends Controller
{
    use ApiResponse;

    /**
     * Store new contact submission.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|min:10',
        ]);

        $contact = Contact::create($validated);

        Notification::route('mail', config('mail.admin_email'))
            ->notify(new SendContactAdminNotification(
                $validated['name'],
                $validated['email'],
                $validated['message']
            ));

        return $this->successResponse(
            $contact,
            'Thank you for reaching out. Your message has been received.',
            201
        );
    }
}