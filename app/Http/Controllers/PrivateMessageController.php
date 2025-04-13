<?php

namespace App\Http\Controllers;

use App\Models\PrivateConversation;
use App\Models\PrivateMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrivateMessageController extends Controller
{
    public function index()
    {
        $conversations = Auth::user()->privateConversations()
            ->with(['messages' => function($query) {
                $query->latest()->limit(1);
            }, 'user1', 'user2']) // Acum aceste relații există
            ->get()
            ->map(function($conversation) {
                $conversation->other_user = $conversation->otherUser(Auth::user());
                $conversation->last_message = $conversation->messages->first();
                return $conversation;
            });

        return view('private-messages.index', compact('conversations'));
    }
    public function show($userId)
    {
        $otherUser = User::findOrFail($userId);
        $currentUser = Auth::user();

        $conversation = PrivateConversation::where(function($query) use ($currentUser, $otherUser) {
            $query->where('user1_id', $currentUser->id)
                  ->where('user2_id', $otherUser->id);
        })->orWhere(function($query) use ($currentUser, $otherUser) {
            $query->where('user1_id', $otherUser->id)
                  ->where('user2_id', $currentUser->id);
        })->firstOrCreate([
            'user1_id' => min($currentUser->id, $otherUser->id),
            'user2_id' => max($currentUser->id, $otherUser->id)
        ]);

        $messages = $conversation->messages()->with('sender')->latest()->paginate(20);

        // Marchează mesajele ca citite
        $conversation->messages()
            ->where('sender_id', $otherUser->id)
            ->where('read', false)
            ->update(['read' => true]);

        return view('private-messages.show', compact('conversation', 'messages', 'otherUser'));
    }

    public function store(Request $request, $userId)
    {
        $request->validate([
            'content' => 'required|string|max:2000'
        ]);

        $otherUser = User::findOrFail($userId);
        $currentUser = Auth::user();

        $conversation = PrivateConversation::where(function($query) use ($currentUser, $otherUser) {
            $query->where('user1_id', $currentUser->id)
                  ->where('user2_id', $otherUser->id);
        })->orWhere(function($query) use ($currentUser, $otherUser) {
            $query->where('user1_id', $otherUser->id)
                  ->where('user2_id', $currentUser->id);
        })->firstOrCreate([
            'user1_id' => min($currentUser->id, $otherUser->id),
            'user2_id' => max($currentUser->id, $otherUser->id)
        ]);

        $message = $conversation->messages()->create([
            'sender_id' => $currentUser->id,
            'content' => $request->content
        ]);

        // Aici poți adăuga notificări sau evenimente

        return back()->with('success', 'Message sent successfully');
    }
}