@extends('layouts.app')

@section('content')
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Edit Expense</h2>
    </x-slot>

    <div class="py-6 max-w-2xl mx-auto">
        <form method="POST" action="{{ route('expenses.update', $expense->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label>Title</label>
                <input type="text" name="title" value="{{ old('title', $expense->title) }}"
                       class="w-full border p-2 rounded" required>
            </div>
            <div class="mb-4">
                <label>Category</label>
                <input type="text" name="category" value="{{ old('category', $expense->category) }}"
                       class="w-full border p-2 rounded" required>
            </div>
            <div class="mb-4">
                <label>Amount</label>
                <input type="number" step="0.01" name="amount" value="{{ old('amount', $expense->amount) }}"
                       class="w-full border p-2 rounded" required>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update Expense</button>
            <a href="{{ route('expenses.expenses') }}" class="ml-2 text-gray-600 hover:underline">Cancel</a>
        </form>
    </div>
@endsection