<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeedController;
use App\Models\FeedItem;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ChatbotController;

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

    Route::get('/quiz', function () {
        return view('dashboard.quiz');
    })->name('dashboard.quiz');
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

require __DIR__.'/auth.php';
