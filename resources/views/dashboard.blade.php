@extends('layouts.app')

@section('content')
<div x-data="{ collapsed: false }" class="flex min-h-screen">

    {{-- PANOU LATERAL --}}
    <div :class="collapsed ? 'w-0 overflow-hidden' : 'w-64'" class="transition-all duration-300 bg-white border-r shadow">
        <div class="p-4">
            <h2 class="text-lg font-bold mb-4">📂 Sections</h2>
            <ul class="space-y-2 text-base">
                <li><a href="{{ route('dashboard') }}" class="text-blue-600 hover:underline">📊 Feed</a></li>
                <li><a href="{{ route('dashboard.quiz') }}" class="text-blue-600 hover:underline">🧠 Daily Financial Quiz</a></li>
                <li><a href="{{ route('profile') }}" class="text-blue-600 hover:underline">👤 Profile</a></li>
                <li><a href="#" class="text-blue-600 hover:underline">⚙️ Settings</a></li>
            </ul>
        </div>
    </div>

    {{-- CONȚINUT PRINCIPAL --}}
    <div class="flex-1 bg-gray-50 p-6 relative">

        {{-- BUTON ASCUNDERE/AFIȘARE PANOU --}}
        <button @click="collapsed = !collapsed" class="absolute top-4 left-4 bg-blue-600 text-white w-8 h-8 rounded-full shadow-lg z-10 flex items-center justify-center">
            <span x-text="collapsed ? '»' : '«'"></span>
        </button>

        {{-- FORMULAR POSTARE --}}
        <div class="max-w-2xl mx-auto">
            <div class="bg-white shadow rounded-xl p-5 mb-6 flex space-x-4">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}" class="w-12 h-12 rounded-full">
                <form method="POST" action="{{ route('feed.post') }}" enctype="multipart/form-data" class="flex-1">
                    @csrf
                    <textarea name="content" rows="2" placeholder="What's on your mind?" class="w-full p-3 border border-gray-300 rounded-xl mb-3 focus:outline-none focus:ring-2 focus:ring-blue-500" required></textarea>

                    <div class="flex items-center justify-between">
                        <input type="file" name="image" class="text-sm text-gray-500">
                        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-full hover:bg-blue-700 transition">
                            Post
                        </button>
                    </div>
                </form>
            </div>

            {{-- FEED --}}
            @foreach ($feed as $item)
            <div class="bg-white rounded-xl shadow p-5 mb-5">
                <div class="flex items-center space-x-3 mb-2">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($item->user->name) }}" class="w-8 h-8 rounded-full">
                    <div class="text-sm text-gray-700 font-semibold">
                        {{ $item->user->name }}
                        <span class="text-gray-400 font-normal text-xs">· {{ $item->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                <p class="text-gray-800 whitespace-pre-line">{{ $item->content }}</p>

                @if($item->image_path)
                    <img src="{{ asset('storage/' . $item->image_path) }}" class="mt-4 rounded-xl max-h-96 object-cover">
                @endif

                <div class="mt-4">
                    <form method="POST" action="{{ route('feed.like', $item) }}">
                        @csrf
                        <button class="text-red-600 hover:underline text-sm">❤️ {{ $item->likes }}</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <a href="{{ url('/chatbot') }}"
       class="fixed bottom-6 right-6 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-5 rounded-full shadow-lg transition duration-300 z-50">
        💬 Chat with AI
    </a>
</div>
@endsection
