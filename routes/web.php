<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeedController;
use App\Models\FeedItem;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ChatbotController;
use Illuminate\Http\Request;
use App\Models\Expense;
use App\Http\Controllers\ExpenseController;

Route::get('/', function () {
    $feed = FeedItem::with('user')->latest()->get();
    return view('welcome', compact('feed'));
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->prefix('dashboard')->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/quiz', [QuizController::class, 'show'])->name('quiz.show');
});


Route::get('/chatbot', [ChatbotController::class, 'show']);
Route::post('/chatbot/talk', [ChatbotController::class, 'talk']);

Route::middleware(['auth'])->group(function () {
    Route::post('/chatbot/clear', [ChatbotController::class, 'clearHistory'])->name('chatbot.clear');
});


Route::middleware(['auth'])->group(function () {
    Route::post('/feed', [FeedController::class, 'store'])->name('feed.post');
    Route::post('/feed/{feed}/like', [FeedController::class, 'like'])->name('feed.like');
});

Route::middleware(['auth'])->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');
    Route::delete('/feed/{feed}', [FeedController::class, 'destroy'])->name('feed.delete');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/quiz', [QuizController::class, 'show'])->name('quiz.show');
    Route::post('/quiz/submit', [QuizController::class, 'submit'])->name('quiz.submit');
});

Route::post('/quiz/share', function (Request $request) {
    $score = (int) $request->input('score');

    if ($score < 0 || $score > 10) {
        return response()->json(['error' => 'Scor invalid.'], 422);
    }

    \App\Models\FeedItem::create([
        'user_id' => auth()->id(),
        'content' => '📊 Am obținut ' . $score . '/10 la quiz-ul de educație financiară! 🧠💸 Tu cât știi?',
        'type' => 'quiz_result',
    ]);

    return response()->json(['status' => 'ok']);
})->middleware('auth'); 

Route::post('/quiz/generate', [ChatbotController::class, 'generateQuizQuestions'])->middleware('auth');

Route::post('/chatbot/analyze-quiz', [\App\Http\Controllers\ChatbotController::class, 'analyzeQuiz'])
    ->middleware('auth');

// Replace the existing logout route with this:
Route::post('/logout', function (Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/expenses', [ExpenseController::class, 'expenses'])->name('expenses.expenses');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('/expenses/{id}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit');
    Route::put('/expenses/{id}', [ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
});

Route::post('/expenses/import', [App\Http\Controllers\ExpenseController::class, 'importMock'])->name('expenses.import.mock');


require __DIR__.'/auth.php';
