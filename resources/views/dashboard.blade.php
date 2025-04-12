@extends('layouts.app')

@section('content')
<div x-data="{ collapsed: false }" class="flex min-h-screen bg-gray-100">

    <!-- Panel lateral retractabil -->
    <div :class="collapsed ? 'w-10' : 'w-64'" class="bg-white border-l shadow-xl h-screen transition-all duration-300 relative flex flex-col items-start px-2 py-4">

        <!-- Buton de toggle în colțul din dreapta sus -->
        <button @click="collapsed = !collapsed"
                class="absolute top-4 right-0 transform translate-x-1/2 bg-blue-600 text-white px-2 py-1 rounded-full hover:bg-blue-700 shadow transition">
            <span x-text="collapsed ? '»' : '«'"></span>
        </button>

        <!-- Conținutul panelului -->
        <div x-show="!collapsed" class="pl-4 pr-2 mt-12 space-y-4 transition-opacity duration-300 w-full">
            <h2 class="text-md font-bold text-gray-800">📂 Secțiuni</h2>
            <ul class="space-y-5 text-base">
                <li><a href="{{ route('dashboard') }}" class="text-blue-600 hover:underline">📊 Statistici</a></li>
                <li><a href="{{ route('dashboard.quiz') }}" class="text-blue-600 hover:underline">🧠 Quiz financiar</a></li>
                <li><a href="#" class="text-blue-600 hover:underline">📚 Resurse</a></li>
                <li><a href="#" class="text-blue-600 hover:underline">⚙️ Setări</a></li>
            </ul>
        </div>
    </div>

    <!-- Conținut principal -->
    <div class="flex-1 p-10">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">👋 Bine ai venit, {{ Auth::user()->name }}</h1>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-blue-100 p-6 rounded-xl shadow-sm">
                <h2 class="text-xl font-semibold text-blue-800 mb-2">📈 Buget</h2>
                <p class="text-gray-700">Ai cheltuit 45% din bugetul lunii.</p>
            </div>
            <div class="bg-green-100 p-6 rounded-xl shadow-sm">
                <h2 class="text-xl font-semibold text-green-800 mb-2">💰 Economii</h2>
                <p class="text-gray-700">Ținta de economisire este atinsă în proporție de 60%.</p>
            </div>
        </div>
    </div>
</div>
@endsection
