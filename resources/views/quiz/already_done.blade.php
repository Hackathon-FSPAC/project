@extends('layouts.dashboard')

@section('content')
<div class="max-w-xl mx-auto mt-10 bg-white shadow p-6 rounded-xl text-center">
    <h2 class="text-2xl font-bold text-green-600 mb-2">✅ Ai completat deja quiz-ul azi!</h2>
    <p class="text-lg text-gray-700 mb-4">Scorul tău: <strong>{{ $score }}/10</strong></p>
    <p class="text-sm text-gray-500">Poți reveni mâine pentru un nou test. 📅</p>
</div>
@endsection
