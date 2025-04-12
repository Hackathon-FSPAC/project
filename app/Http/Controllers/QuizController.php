namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuizResult;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class QuizController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $today = Carbon::today();

        $existing = QuizResult::where('user_id', $user->id)
                    ->whereDate('completed_at', $today)
                    ->first();

        if ($existing) {
            return view('quiz.already_done', ['score' => $existing->score]);
        }

        return view('quiz.start'); // pagina cu întrebări
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

    return redirect()->route('quiz.show');
}
}
