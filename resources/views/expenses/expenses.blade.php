@extends('layouts.panel')

@section('content')
<div class="py-6 max-w-3xl mx-auto px-4">

    <h2 class="text-2xl font-bold mb-6">📒 Expense Tracker</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 border border-green-300 p-4 rounded-xl mb-6 shadow">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('expenses.store') }}" class="bg-white shadow rounded-xl p-6 space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1 text-gray-700">📌 Titlu</label>
            <input type="text" name="title" class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1 text-gray-700">📂 Categorie</label>
            <select name="category" class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <option value="" disabled selected>Alege o categorie</option>
                <option value="Food">🍔 Food</option>
                <option value="Rent">🏠 Rent</option>
                <option value="Entertainment">🎮 Entertainment</option>
                <option value="Utilities">💡 Utilities</option>
                <option value="Transport">🚗 Transport</option>
                <option value="Health">🩺 Health</option>
                <option value="Education">📚 Education</option>
                <option value="Others">🔧 Others</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium mb-1 text-gray-700">💸 Suma (pozitiv = venit, negativ = cheltuială)</label>
            <input type="number" step="0.01" name="amount" class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>
        <div class="text-right">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition shadow">
                ➕ Adaugă tranzacție
            </button>
        </div>
    </form>

    <div class="mt-10">
        <h3 class="text-lg font-semibold mb-3">🧾 Tranzacțiile tale</h3>

        <ul class="space-y-4">
            @foreach($expenses as $expense)
                <li class="bg-white p-4 rounded-xl shadow flex justify-between items-center">
                    <div>
                        <div class="font-semibold text-gray-800">{{ $expense->title }} <span class="text-sm text-gray-500">({{ $expense->category }})</span></div>
                        <div class="text-sm text-gray-500">{{ $expense->created_at->format('Y-m-d') }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold {{ $expense->amount >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $expense->amount >= 0 ? '+' : '' }}${{ number_format($expense->amount, 2) }}
                        </div>
                        <div class="mt-2 flex gap-3 justify-end text-sm">
                            <a href="{{ route('expenses.edit', $expense->id) }}" class="text-yellow-600 hover:underline">✏️ Editare</a>
                            <form method="POST" action="{{ route('expenses.destroy', $expense->id) }}" onsubmit="return confirm('Ești sigur că vrei să ștergi această tranzacție?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">🗑️ Șterge</button>
                            </form>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>

        <div class="mt-6 bg-gray-50 border border-gray-200 rounded-xl p-4 text-right">
            <strong>Total:</strong>
            <span class="text-xl font-bold {{ $total >= 0 ? 'text-green-600' : 'text-red-600' }}">
                ${{ number_format($total, 2) }}
            </span>
        </div>
    </div>
</div>
@endsection
