@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row g-4">
        <!-- Chat principal -->
        <div class="col-lg-8">
            <div class="card shadow rounded-4">
                <div class="card-header bg-white d-flex align-items-center py-3 border-0">
                    <div class="avatar me-3">
                        <img src="{{ $otherUser->profile_photo_url }}" alt="{{ $otherUser->name }}" class="rounded-circle" width="48" height="48">
                    </div>
                    <div>
                        <h5 class="mb-0">Conversație cu {{ $otherUser->name }}</h5>
                        <small class="text-muted">
                            @if($messages->count() > 0)
                                Ultimul mesaj: {{ $messages->first()->created_at->diffForHumans() }}
                            @else
                                Nu există mesaje
                            @endif
                        </small>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="chat-messages px-4 py-3" style="max-height: calc(100vh - 300px); overflow-y: auto;">
                        @forelse($messages->reverse() as $message)
                            <div class="chat-message @if($message->sender_id == Auth::id()) outgoing @else incoming @endif mb-3">
                                <div class="message-bubble @if($message->sender_id == Auth::id()) bg-primary text-white @else bg-light @endif">
                                    <div class="message-content">
                                        {{ $message->content }}
                                    </div>
                                    <div class="message-time text-end small @if($message->sender_id == Auth::id()) text-white-50 @else text-muted @endif">
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
                            <div class="text-center py-5 text-muted">
                                Nu există mesaje în această conversație. Trimite primul mesaj!
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="card-footer bg-white py-3 border-0">
                    <form action="{{ route('private-messages.store', $otherUser) }}" method="POST" class="message-form">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="content" class="form-control border-0 py-3" placeholder="Scrie un mesaj..." required>
                            <button class="btn btn-primary px-4" type="submit">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Detalii utilizator -->
        <div class="col-lg-4">
            <div class="card shadow rounded-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0">Detalii utilizator</h5>
                </div>
                <div class="card-body text-center">
                    <div class="avatar mb-3">
                        <img src="{{ $otherUser->profile_photo_url }}" alt="{{ $otherUser->name }}" class="rounded-circle shadow" width="100" height="100">
                    </div>
                    <h4>{{ $otherUser->name }}</h4>
                    <p class="text-muted">{{ $otherUser->email }}</p>
                    <div class="d-flex justify-content-center mt-3">
                        <button class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .chat-messages {
        scroll-behavior: smooth;
    }

    .message-bubble {
        border-radius: 1rem;
        padding: 12px 16px;
        max-width: 80%;
        position: relative;
        word-wrap: break-word;
    }

    .incoming .message-bubble {
        background-color: #f1f3f5;
        border-bottom-left-radius: 0.5rem;
    }

    .outgoing .message-bubble {
        background-color: #0d6efd;
        border-bottom-right-radius: 0.5rem;
        margin-left: auto;
    }

    .message-time {
        font-size: 0.75rem;
        margin-top: 4px;
    }

    .avatar img {
        object-fit: cover;
    }

    .message-form .form-control:focus {
        box-shadow: none;
    }

    @media (max-width: 768px) {
        .chat-messages {
            padding: 1rem !important;
        }

        .card-header h5 {
            font-size: 1.1rem;
        }

        .message-bubble {
            max-width: 100%;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatContainer = document.querySelector('.chat-messages');
        chatContainer.scrollTop = chatContainer.scrollHeight;
    });
</script>
@endsection
