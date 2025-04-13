@extends('layouts.app')

@section('content')
<div class="py-6 max-w-4xl mx-auto">
    <h2 class="text-xl font-semibold mb-4">Some custom financial insights based on your own expenses</h2>

    <a href="{{ route('expenses.expenses') }}"
       class="inline-block mb-6 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 transition">
        ← Back to Expenses
    </a>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded shadow">
            <h3 class="text-gray-500 text-sm">Total Balance</h3>
            <p class="text-xl font-bold {{ $analysis['statistics']['totalBalance'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                ${{ number_format($analysis['statistics']['totalBalance'], 2) }}
            </p>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <h3 class="text-gray-500 text-sm">Income</h3>
            <p class="text-xl font-bold text-green-600">
                ${{ number_format($analysis['statistics']['totalIncome'], 2) }}
            </p>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <h3 class="text-gray-500 text-sm">Expenses</h3>
            <p class="text-xl font-bold text-red-600">
                ${{ number_format($analysis['statistics']['totalExpenses'], 2) }}
            </p>
        </div>
    </div>

    <div class="bg-white p-4 rounded shadow mb-6">
        <h3 class="text-gray-500 text-sm">Savings Rate</h3>
        <div class="flex items-center mt-2">
            <div class="w-full bg-gray-200 rounded-full h-4">
                <div class="bg-blue-600 h-4 rounded-full" style="width: {{ min($analysis['statistics']['savingsRate'], 100) }}%"></div>
            </div>
            <span class="ml-2 font-bold">{{ $analysis['statistics']['savingsRate'] }}%</span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Chart -->
        <div class="bg-white p-4 rounded shadow">
            <h3 class="font-bold mb-4">Expense Categories</h3>
            <canvas id="expensePieChart" width="400" height="300"></canvas>

            @if(count($analysis['expenseCategories'] ?? []) > 0)
                <div class="mt-4 space-y-2">
                    @foreach($analysis['expenseCategories'] as $category)
                        <div class="flex justify-between text-sm">
                            <span>{{ $category['category'] }}</span>
                            <span>${{ number_format($category['total'], 2) }} ({{ $category['percentage'] }}%)</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center text-gray-500 mt-4">No expense data available</div>
            @endif
        </div>

        <!-- Income Categories Chart -->
        <div class="bg-white p-4 rounded shadow">
            <h3 class="font-bold mb-4">Income Categories</h3>
            <canvas id="incomePieChart" width="400" height="300"></canvas>

            @if(count($analysis['incomeCategories'] ?? []) > 0)
                <div class="mt-4 space-y-2">
                    @foreach($analysis['incomeCategories'] as $category)
                        <div class="flex justify-between text-sm">
                            <span>{{ $category['category'] }}</span>
                            <span>${{ number_format($category['total'], 2) }} ({{ $category['percentage'] }}%)</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center text-gray-500 mt-4">No income data available</div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Expense Pie Chart
                const expenseCtx = document.getElementById('expensePieChart').getContext('2d');
                const expenseCategories = @json($analysis['expenseCategories'] ?? []);

                if (expenseCategories && expenseCategories.length > 0) {
                    const expenseLabels = expenseCategories.map(item => item.category);
                    const expenseValues = expenseCategories.map(item => parseFloat(item.total));

                    console.log('Expense Labels:', expenseLabels);
                    console.log('Expense Data:', expenseValues);

                    new Chart(expenseCtx, {
                        type: 'pie',
                        data: {
                            labels: expenseLabels,
                            datasets: [{
                                label: 'Expense Categories',
                                data: expenseValues,
                                backgroundColor: [
                                    '#EF4444', '#F59E0B', '#F97316', '#EC4899', '#A78BFA',
                                    '#6366F1', '#3B82F6', '#22D3EE', '#10B981', '#84CC16'
                                ],
                                borderColor: '#fff',
                                borderWidth: 2,
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.raw || 0;
                                            const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                            const percentage = Math.round((value / total) * 100);
                                            return `${label}: $${value.toFixed(2)} (${percentage}%)`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                } else {
                    document.getElementById('expensePieChart').parentNode.querySelector('canvas').style.display = 'none';
                }

                // Income Pie Chart
                const incomeCtx = document.getElementById('incomePieChart').getContext('2d');
                const incomeCategories = @json($analysis['incomeCategories'] ?? []);

                if (incomeCategories && incomeCategories.length > 0) {
                    const incomeLabels = incomeCategories.map(item => item.category);
                    const incomeValues = incomeCategories.map(item => parseFloat(item.total));

                    console.log('Income Labels:', incomeLabels);
                    console.log('Income Data:', incomeValues);

                    new Chart(incomeCtx, {
                        type: 'pie',
                        data: {
                            labels: incomeLabels,
                            datasets: [{
                                label: 'Income Categories',
                                data: incomeValues,
                                backgroundColor: [
                                    '#10B981', '#34D399', '#22D3EE', '#3B82F6', '#6366F1',
                                    '#A78BFA', '#8B5CF6', '#EC4899', '#F472B6', '#FBBF24'
                                ],
                                borderColor: '#fff',
                                borderWidth: 2,
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.raw || 0;
                                            const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                            const percentage = Math.round((value / total) * 100);
                                            return `${label}: $${value.toFixed(2)} (${percentage}%)`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                } else {
                    document.getElementById('incomePieChart').parentNode.querySelector('canvas').style.display = 'none';
                }
            });
        </script>
    @endpush


    <div class="bg-white p-4 rounded shadow">
        <h3 class="font-bold mb-4">Financial Insights</h3>
        <div class="prose">
            {!! nl2br(e($analysis['insights'])) !!}
        </div>
    </div>
</div>
@endsection

