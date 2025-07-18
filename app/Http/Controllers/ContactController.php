<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notifications\ContactMessageNotification;
use App\Models\User;
use App\Models\ContactMessage;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        // Save the message to the database
        $contactMessage = ContactMessage::create($validated);

        // Get all admin users (assuming 'is_admin' boolean column)
        $admins = User::where('is_admin', true)->get();

        // Send notification to all admins
        foreach ($admins as $admin) {
            $admin->notify(new ContactMessageNotification($validated['name'], $validated['email'], $validated['message']));
        }

        return back()->with('success', 'Your message has been sent!');
    }
}
