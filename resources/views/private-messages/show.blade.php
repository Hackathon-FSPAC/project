@extends('layouts.panel') {{-- Sau alt layout --}}

@section('content')
<div class="max-w-2xl mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Conversation with {{ $conversation->getOtherUserAttribute(auth()->id())->name }}</h1>
    
    <div class="bg-white rounded-lg shadow p-4 mb-4">
        @foreach($conversation->messages as $message)
            <div class="mb-4 {{ $message->sender_id == auth()->id() ? 'text-right' : '' }}">
                <p class="text-sm {{ $message->sender_id == auth()->id() ? 'text-blue-600' : 'text-gray-600' }}">
                    {{ $message->sender->name }}
                </p>
                <p class="p-2 rounded-lg inline-block 
                    {{ $message->sender_id == auth()->id() ? 'bg-blue-100' : 'bg-gray-100' }}">
                    {{ $message->body }}
                </p>
                <p class="text-xs text-gray-500">{{ $message->created_at->diffForHumans() }}</p>
            </div>
        @endforeach
    </div>
    
    <form action="{{ route('private-messages.send', $conversation->id) }}" method="POST">
        @csrf
        <div class="flex">
            <input type="text" name="body" class="flex-1 border rounded-l-lg p-2" placeholder="Type your message...">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-r-lg">Send</button>
        </div>
    </form>
</div>
@endsection