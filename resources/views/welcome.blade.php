{{-- resources/views/home.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-tr from-blue-100 to-purple-200 p-6">
        <div class="bg-white shadow-2xl rounded-2xl p-10 max-w-xl text-center space-y-6">
            <h1 class="text-4xl font-bold text-gray-800">👋 Bun venit!</h1>
            <p class="text-gray-600 text-lg">
                Administrează-ți bugetul, învață despre bani și preia controlul asupra finanțelor tale.
            </p>

            <div class="space-x-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="bg-blue-600 text-white px-6 py-2 rounded-xl hover:bg-blue-700 transition">
                        Mergi la Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="bg-blue-500 text-white px-6 py-2 rounded-xl hover:bg-blue-600 transition">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="bg-gray-300 text-gray-800 px-6 py-2 rounded-xl hover:bg-gray-400 transition">
                        Register
                    </a>
                @endauth
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-xl mx-auto py-10 px-4">

    {{-- Formular pentru postare --}}
    @if(Auth::check())
    <div class="bg-white p-5 rounded-xl shadow mb-6">
        <form method="POST" action="{{ route('feed.post') }}" enctype="multipart/form-data">
            @csrf
            <textarea name="content" rows="3" placeholder="Ce ai în minte?" class="w-full p-2 border rounded mb-3" required></textarea>

            <input type="file" name="image" class="mb-3">

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Postează
            </button>
        </form>
    </div>
    @endif

    {{-- Lista de postări --}}
    @foreach ($feed as $item)
    <div class="bg-white rounded-xl shadow p-5 mb-4">
        <p class="text-gray-800 whitespace-pre-line">{{ $item->content }}</p>

        @if($item->image_path)
            <img src="{{ asset('storage/' . $item->image_path) }}" class="mt-3 rounded max-h-96 object-cover" alt="Imagine postare">
        @endif

        <div class="text-sm text-gray-500 mt-2 flex justify-between items-center">
            <span>Postat de <strong>{{ $item->user->name }}</strong> · {{ $item->created_at->diffForHumans() }}</span>

            <form method="POST" action="{{ route('feed.like', $item) }}">
                @csrf
                <button class="text-red-600 hover:underline">❤️ {{ $item->likes }}</button>
            </form>
        </div>
    </div>
    @endforeach

</div>
@endsection
