<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;


class ExpenseController extends Controller
{
    public function expenses()
    {
        $expenses = Expense::where('user_id', Auth::id())->latest()->get();
        $total = $expenses->sum('amount');

        return view('expenses.expenses', compact('expenses', 'total'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|not_in:0',
        ]);

        $validated['user_id'] = Auth::id();

        Expense::create($validated);

        return redirect()->back()->with('success', 'Expense added!');
    }

    public function edit($id)
    {
        $expense = Expense::where('user_id', Auth::id())->findOrFail($id);
        return view('expenses.edit', compact('expense'));
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|not_in:0',
        ]);

        $expense->update($validated);

        return redirect()->route('expenses.expenses')->with('success', 'Expense updated!');
    }

    public function destroy($id)
    {
        $expense = Expense::where('user_id', Auth::id())->findOrFail($id);
        $expense->delete();

        return redirect()->back()->with('success', 'Expense deleted!');
    }

}