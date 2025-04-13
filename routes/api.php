Route::get('/mock-transactions', function () {
    return response()->json([
        [
            'title' => 'Starbucks',
            'category' => 'Food & Drink',
            'amount' => -18.50,
            'created_at' => now()->subDays(1)->toDateString()
        ],
        [
            'title' => 'Netflix',
            'category' => 'Entertainment',
            'amount' => -49.99,
            'created_at' => now()->subDays(2)->toDateString()
        ],
        [
            'title' => 'Salary',
            'category' => 'Income',
            'amount' => 3200,
            'created_at' => now()->subDays(3)->toDateString()
        ]
    ]);
});
