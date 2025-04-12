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
