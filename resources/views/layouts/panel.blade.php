<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name') }} | Panel</title>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>
<body class="bg-gray-100">

<div x-data="{ collapsed: false }" class="flex min-h-screen">

    {{-- Panou lateral --}}
    <div :class="collapsed ? 'w-0 overflow-hidden' : 'w-64'" class="transition-all duration-300 bg-white border-r shadow">
        <div class="p-4">
            <h2 class="text-lg font-bold mb-4">📂 Sections</h2>
            <ul class="space-y-2 text-base">
                <li><a href="{{ route('dashboard') }}" class="text-blue-600 hover:underline">📊 Feed</a></li>
                <li><a href="{{ route('quiz.show') }}" class="text-blue-600 hover:underline">🧠 Daily Financial Quiz</a></li>
                <li><a href="{{ route('profile') }}" class="text-blue-600 hover:underline">👤 Profile</a></li>
                <li><a href="{{ route('expenses.expenses') }}" class="text-blue-600 hover:underline">💸 Your Expenses</a></li>
                <li><a href="{{ route('private-messages.index') }}" class="text-blue-600 hover:underline"><i class="fas fa-envelope"></i>💬 Messages</a></li>
            </ul>
        </div>
    </div>

    {{-- Conținut principal --}}
    <div class="flex-1 p-6 relative">

        {{-- Buton ascundere panou lateral --}}
        <button @click="collapsed = !collapsed" class="absolute top-4 left-4 bg-blue-600 text-white w-8 h-8 rounded-full shadow z-10 flex items-center justify-center">
            <span x-text="collapsed ? '»' : '«'"></span>
        </button>

        {{-- Bara de sus cu logout --}}
        <div class="absolute top-4 right-4">
            <div class="relative group">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-2 bg-gradient-to-r from-pink-500 to-red-500 text-white px-5 py-2.5 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-[1.03] active:scale-95 group-hover:from-pink-600 group-hover:to-red-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:animate-pulse" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span class="font-medium">Logout</span>
                        <span class="absolute -right-1 -top-1 h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75 group-hover:opacity-100"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                        </span>
                    </button>
                </form>
                <div class="absolute -bottom-8 left-1/2 transform -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <div class="bg-gray-800 text-white text-xs px-2 py-1 rounded whitespace-nowrap">Sesiune curentă: {{ Auth::user()->email }}</div>
                    <div class="w-3 h-3 bg-gray-800 rotate-45 absolute -top-1.5 left-1/2 -translate-x-1/2"></div>
                </div>
            </div>
        </div>

        {{-- Conținutul paginii --}}
        @yield('content')
    </div>
</div>

</body>
</html>
