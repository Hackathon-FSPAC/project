@extends('layouts.panel')

@section('content')
<div class="max-w-2xl mx-auto py-10 px-4">
    <h1 class="text-2xl font-bold mb-6">💬 Mesaje private</h1>

    <div class="space-y-4">
        @forelse($conversations as $conversation)
            <a href="{{ route('private-messages.show', $conversation->other_user) }}"
               class="flex items-start gap-4 bg-white p-4 rounded-xl shadow hover:shadow-md transition duration-300 border border-gray-200 hover:border-blue-400">

                {{-- Avatar user --}}
                <img src="{{ $conversation->other_user->profile_photo 
                            ? asset('storage/' . $conversation->other_user->profile_photo) 
                            : 'https://ui-avatars.com/api/?name=' . urlencode($conversation->other_user->name) }}"
                     alt="{{ $conversation->other_user->name }}"
                     class="w-12 h-12 rounded-full object-cover">

                {{-- Conținut --}}
                <div class="flex-1">
                    <div class="flex justify-between items-center">
                        <h5 class="text-lg font-semibold text-gray-900">
                            {{ $conversation->other_user->name }}
                        </h5>
                        <small class="text-sm text-gray-500">
                            @if($conversation->last_message)
                                {{ $conversation->last_message->created_at->diffForHumans() }}
                            @else
                                Nicio activitate
                            @endif
                        </small>
                    </div>

                    <p class="text-sm text-gray-700 mt-1">
                        @if($conversation->last_message)
                            {{ Str::limit($conversation->last_message->content, 60) }}
                        @else
                            ✉️ Începe o conversație
                        @endif
                    </p>
                </div>
            </a>
        @empty
            <p class="text-gray-500 text-center">Nu ai încă conversații.</p>
        @endforelse
    </div>
</div>
@endsection
