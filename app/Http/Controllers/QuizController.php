<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuizResult;
use App\Models\FeedItem;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class QuizController extends Controller
{
    public function show()
{
    $today = \Carbon\Carbon::today();
    $user = \Auth::user();

    $already = \App\Models\QuizResult::where('user_id', $user->id)
        ->whereDate('completed_at', $today)
        ->first();

    if ($already) {
        return view('quiz.already_done', ['score' => $already->score]);
    }

    return view('quiz.start');
}

    public function submit(Request $request)
{
    $today = Carbon::today();
    $userId = Auth::id();

    // ❗ Verifică dacă deja există un quiz azi
    $alreadyDone = QuizResult::where('user_id', $userId)
        ->whereDate('completed_at', $today)
        ->first();

    if ($alreadyDone) {
        return redirect()->route('quiz.show')->with('error', 'Ai completat deja quiz-ul azi.');
    }

    // ✅ Calculează scorul și salvează
    $request->validate([
        'score' => 'required|integer|min:0|max:10',
    ]);

    QuizResult::create([
        'user_id' => $userId,
        'score' => $request->score,
        'completed_at' => now(),
    ]);

    FeedItem::create([
    'user_id' => Auth::id(),
    'content' => '📊 Tocmai am completat quiz-ul financiar și am obținut scorul de ' . $request->score . '/10!',
    'type' => 'quiz_result', // opțional: să știi că e o postare automată
    ]);

    return redirect()->route('quiz.show');
}
}
