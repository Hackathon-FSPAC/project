{{-- resources/views/layouts/panel.blade.php --}}
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
        <button @click="collapsed = !collapsed" class="absolute top-4 left-4 bg-blue-600 text-white w-8 h-8 rounded-full shadow z-10 flex items-center justify-center">
            <span x-text="collapsed ? '»' : '«'"></span>
        </button>

        {{-- Conținutul paginii --}}
        @yield('content')
    </div>
</div>

</body>
</html>
