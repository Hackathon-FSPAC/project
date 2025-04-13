@extends('layouts.panel')

@section('content')
<div class="max-w-5xl mx-auto py-10 px-4 grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Chat principal --}}
    <div class="lg:col-span-2 bg-white shadow rounded-2xl overflow-hidden flex flex-col">
        <div class="flex items-center gap-4 p-4 border-b">
            <img src="{{ $otherUser->profile_photo_url }}" alt="{{ $otherUser->name }}" class="w-12 h-12 rounded-full object-cover">
            <div>
                <h2 class="font-semibold text-lg">Conversație cu {{ $otherUser->name }}</h2>
                <p class="text-sm text-gray-500">
                    @if($messages->count() > 0)
                        Ultimul mesaj: {{ $messages->first()->created_at->diffForHumans() }}
                    @else
                        Nu există mesaje
                    @endif
                </p>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-4" id="chat-scroll">
            @forelse($messages->reverse() as $message)
                <div class="flex {{ $message->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-xs md:max-w-md px-4 py-2 rounded-xl shadow
                        {{ $message->sender_id === Auth::id() ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-900' }}">
                        <p>{{ $message->content }}</p>
                        <div class="text-xs mt-1 text-right {{ $message->sender_id === Auth::id() ? 'text-blue-200' : 'text-gray-500' }}">
                            {{ $message->created_at->format('H:i') }}
                            @if($message->sender_id == Auth::id())
                                @if($message->read)
                                    <i class="fas fa-check-double ms-1"></i>
                                @else
                                    <i class="fas fa-check ms-1"></i>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500 py-10">
                    Nu există mesaje în această conversație. Trimite primul mesaj! ✉️
                </div>
            @endforelse
        </div>

        <form action="{{ route('private-messages.store', $otherUser) }}" method="POST" class="border-t p-4">
            @csrf
            <div class="flex gap-3">
                <input type="text" name="content" placeholder="Scrie un mesaj..." required
                    class="flex-1 border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-blue-600 text-white px-5 py-3 rounded-xl hover:bg-blue-700 transition">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </form>
    </div>

    {{-- Detalii utilizator --}}
    <div class="bg-white shadow rounded-2xl p-6 text-center">
        <img src="{{ $otherUser->profile_photo_url }}" alt="{{ $otherUser->name }}"
             class="w-24 h-24 rounded-full mx-auto mb-4 shadow object-cover">
        <h3 class="text-xl font-semibold">{{ $otherUser->name }}</h3>
        <p class="text-gray-500">{{ $otherUser->email }}</p>
        <div class="mt-4">
            <button class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-ellipsis-h"></i>
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const chatContainer = document.getElementById('chat-scroll');
        chatContainer.scrollTop = chatContainer.scrollHeight;
    });
</script>
@endsection
