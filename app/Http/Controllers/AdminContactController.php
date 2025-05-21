<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminContactController extends Controller
{
    /**
     * Display a listing of the contact messages.
     */
    public function index(Request $request)
    {
        $query = ContactMessage::query();
        
        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Search by email, name, or subject
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('order_id', 'like', "%{$search}%");
            });
        }
        
        // Sort by date (latest first by default)
        $query->orderBy('created_at', 'desc');
        
        // Paginate the results
        $messages = $query->paginate(15);
        
        return view('admin.contact.index', compact('messages'));
    }

    /**
     * Display the specified contact message.
     */
    public function show($id)
    {
        $message = ContactMessage::findOrFail($id);
        
        // Mark as read if it's in pending state
        if ($message->status === 'pending') {
            $message->status = 'read';
            $message->save();
        }
        
        return view('admin.contact.show', compact('message'));
    }

    /**
     * Update the contact message status and add admin notes.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'nullable|string',
            'status' => 'required|in:pending,read,replied',
        ]);
        
        $message = ContactMessage::findOrFail($id);
        $message->admin_notes = $request->admin_notes;
        $message->status = $request->status;
        $message->admin_id = Auth::guard('admin')->id();
        $message->save();
        
        return redirect()->route('admin.contact.show', $message->id)
            ->with('success', 'Pesan berhasil diperbarui');
    }

    /**
     * Reply to a contact message.
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'reply_message' => 'required|string',
        ]);
        
        $message = ContactMessage::findOrFail($id);
        
        // Send email reply (this would be implemented in a real app)
        // Mail::to($message->email)->send(new ContactReply($message, $request->reply_message));
        
        // Update message status
        $message->status = 'replied';
        $message->admin_notes = ($message->admin_notes ? $message->admin_notes . "\n\n" : '') . 
                               "Replied on " . now()->format('Y-m-d H:i:s') . ":\n" . $request->reply_message;
        $message->admin_id = Auth::guard('admin')->id();
        $message->save();
        
        return redirect()->route('admin.contact.show', $message->id)
            ->with('success', 'Balasan berhasil dikirim');
    }

    /**
     * Delete a contact message.
     */
    public function destroy($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();
        
        return redirect()->route('admin.contact.index')
            ->with('success', 'Pesan berhasil dihapus');
    }
}
