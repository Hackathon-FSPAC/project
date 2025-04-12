@extends('layouts.dashboard')

@section('content')
<div class="max-w-xl mx-auto mt-10 bg-white shadow p-6 rounded-xl text-center">
    <h2 class="text-2xl font-bold mb-4">🧠 Quiz financiar</h2>

    {{-- aici vine quiz-ul propriu-zis (cu JS sau formulare normale) --}}
    {{-- simulare scor final --}}
    <form method="POST" action="{{ route('quiz.submit') }}">
        @csrf
        <label for="score">Scor obținut:</label>
        <input type="number" name="score" min="0" max="10" class="border p-2 rounded" required>
        <button class="mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Trimite</button>
    </form>
</div>
@endsection
