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

    return view('quiz.start');
}

    public function submit(Request $request)
{
    $today = Carbon::today();
    $userId = Auth::id();
    
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
