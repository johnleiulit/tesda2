<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'message' => 'required|string|max:5000',
        ]);

        ContactMessage::create($validated);

        try {
            Mail::raw(
                "Name: {$validated['name']}\nEmail: {$validated['email']}\n\nMessage:\n{$validated['message']}",
                function ($message) use ($validated) {
                    $message->to(config('mail.from.address'))
                        ->subject('Contact form: ' . $validated['name']);
                    $message->replyTo($validated['email'], $validated['name']);
                }
            );
        } catch (\Exception $e) {
            Log::warning('Contact email failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Thank you! Your message has been sent.');
    }

    public function index()
    {
        $messages = ContactMessage::latest()->paginate(15);
        return view('admin.contact.index', compact('messages'));
    }
     public function markAsRead($id)
    {
        $message = ContactMessage::findOrFail($id);
        
        if (!$message->is_read) {
            $message->update(['is_read' => true]);
        }

        return back();
    }

    // Add this method to show individual message details
    public function show($id)
    {
        $message = ContactMessage::findOrFail($id);
        
        // Mark as read when viewing
        if (!$message->is_read) {
            $message->update(['is_read' => true]);
        }

        return view('admin.contact.show', compact('message'));
    }
    public function destroy($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();
        
        return back()->with('success', 'Message deleted successfully.');
    }
}