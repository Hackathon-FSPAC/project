@extends('layouts.panel')

@section('content')
    
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Expense Tracker</h2>
    </x-slot>

    <div class="py-6 max-w-2xl mx-auto">
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('expenses.store') }}">
            @csrf
            <div class="mb-4">
                <label>Title</label>
                <input type="text" name="title" class="w-full border p-2 rounded" required>
            </div>
            <div class="mb-4">
                <label>Category</label>
                <input type="text" name="category" class="w-full border p-2 rounded" required>
            </div>
            <div class="mb-4">
                <label>Amount (positive = income, negative = expense)</label>
                <input type="number" name="amount" step="0.01" class="w-full border p-2 rounded" required>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Add Expense</button>
        </form>

        <h3 class="text-lg font-bold mt-8">Your Expenses</h3>
            <ul class="mt-2 space-y-2">
                @foreach($expenses as $expense)
                    <li class="border p-3 rounded bg-white">
                        <div class="flex justify-between items-center">
                            <div>
                                <strong>{{ $expense->title }}</strong> ({{ $expense->category }})<br>
                                <small>{{ $expense->created_at->format('Y-m-d') }}</small>
                            </div>
                            <div class="text-right">
                                <div class="{{ $expense->amount >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $expense->amount >= 0 ? '+' : '' }}${{ number_format($expense->amount, 2) }}
                                </div>
                                <div class="mt-2 flex gap-2">
                                    <a href="{{ route('expenses.edit', $expense->id) }}"
                                       class="text-sm text-yellow-600 hover:underline">Edit</a>

                                    <form method="POST" action="{{ route('expenses.destroy', $expense->id) }}"
                                          onsubmit="return confirm('Are you sure you want to delete this expense?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:underline">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>


            <div class="mt-4 p-4 bg-gray-100 rounded">
            <strong>Total:</strong>
            <span class="{{ $total >= 0 ? 'text-green-600' : 'text-red-600' }}">
                ${{ number_format($total, 2) }}
            </span>
        </div>
    </div>
    @endsection