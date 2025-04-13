@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Private Messages</h1>

    <div class="list-group">
        @foreach($conversations as $conversation)
            <a href="{{ route('private-messages.show', $conversation->other_user) }}"
               class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1">{{ $conversation->other_user->name }}</h5>
                    <small>
                        @if($conversation->last_message)
                            {{ $conversation->last_message->created_at->diffForHumans() }}
                        @else
                            No messages yet
                        @endif
                    </small>
                </div>
                <p class="mb-1">
                    @if($conversation->last_message)
                        {{ Str::limit($conversation->last_message->content, 50) }}
                    @else
                        Start a conversation
                    @endif
                </p>
            </a>
        @endforeach
    </div>
</div>
@endsection