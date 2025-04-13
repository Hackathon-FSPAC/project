<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Conversation;
use App\Models\Message;

class ChatbotController extends Controller
{
    public function show()
    {
        // Get previous messages if user is authenticated
        $messages = [];
        if (Auth::check()) {
            $conversation = $this->getUserConversation();
            $messages = $conversation->messages()->orderBy('created_at')->get();
        }

        return view('chatbot.chatbot', compact('messages'));
    }

    public function talk(Request $request)
    {
        try {
            $userMessage = $request->input('message');

            if (empty(trim($userMessage))) {
                return response()->json(['reply' => 'Mesajul este gol. Încearcă din nou.']);
            }
            $apiKey = env('GEMINI_API_KEY');

            // Get or create conversation for authenticated users
            $conversation = null;
            $messageHistory = [];

            if (Auth::check()) {
                $conversation = $this->getUserConversation();

                // Save user message to database
                $message = new Message([
                    'content' => $userMessage,
                    'role' => 'user'
                ]);
                $conversation->messages()->save($message);

                // Get previous messages to build context (limit to last 10 for API size constraints)
                $messageHistory = $conversation->messages()
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get()
                    ->reverse()
                    ->map(function ($msg) {
                        return [
                            'role' => $msg->role,
                            'parts' => [['text' => $msg->content]]
                        ];
                    })
                    ->toArray();
            }

            // Build content array for API request
            $contents = [
                [
                    'role' => 'model',
                    'parts' => [
                        [
                            'text' => "You are a financial assistant. Always respond with a focus on financial literacy, economics, budgeting, investments, or financial planning unless otherwise asked. Be accurate and clear. Avoid using Markdown formatting or special characters like ** for bold. Write responses using plain text, paragraphs and - lines."

                        ]
                    ]
                ]
            ];

            // Add message history to provide context if available
            if (!empty($messageHistory)) {
                $contents = array_merge($contents, $messageHistory);
            } else {
                // If no history, just add the current message
                if (empty(trim($userMessage))) {
                    return response()->json(['reply' => 'Mesajul este gol. Încearcă din nou.']);
                }
                $contents[] = [
                    'role' => 'user',
                    'parts' => [['text' => $userMessage]]
                ];
            }

            $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $apiKey;

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, [
                'contents' => $contents
            ]);

            Log::info('Gemini API Response', ['status' => $response->status()]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? "Sorry, no response.";

                // Save AI response to database if user is authenticated
                if (Auth::check() && $conversation) {
                    $message = new Message([
                        'content' => $reply,
                        'role' => 'model'
                    ]);
                    $conversation->messages()->save($message);
                }

                return response()->json(['reply' => $reply]);
            } else {
                Log::error('Gemini API Error', ['error' => $response->json()]);
                return response()->json([
                    'reply' => 'Error: ' . ($response->json()['error']['message'] ?? 'Unknown error'),
                ], 200);
            }
        } catch (\Exception $e) {
            Log::error('Chatbot Error', ['message' => $e->getMessage()]);
            return response()->json(['reply' => 'Server error: ' . $e->getMessage()], 200);
        }
    }

    /**
     * Get or create a conversation for the current user
     */
    private function getUserConversation()
    {
        $user = Auth::user();
        $conversation = Conversation::firstOrCreate(
            ['user_id' => $user->id],
            ['title' => 'Financial Assistant Chat']
        );

        return $conversation;
    }

    /**
     * Clear conversation history
     */
    public function clearHistory()
    {
        if (Auth::check()) {
            $conversation = $this->getUserConversation();
            $conversation->messages()->delete();
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error'], 401);
    }

    public function generateQuizQuestions(Request $request)
    {
        try {
            $prompt = $request->input('message');

            if (empty(trim($prompt))) {
                return response()->json(['reply' => 'Mesajul este gol.']);
            }

            $apiKey = env('GEMINI_API_KEY');
            $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $apiKey;

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [['text' => $prompt]]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                // get first candidate from gemini responses, get its content (message)
                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? "Eroare: răspuns gol.";
                return response()->json(['reply' => $reply]);
            }

            return response()->json(['reply' => 'Eroare de la Gemini: ' . ($response->json()['error']['message'] ?? 'Necunoscută')]);

        } catch (\Exception $e) {
            return response()->json(['reply' => 'Eroare server: ' . $e->getMessage()]);
        }
    }

    public function analyzeQuiz(Request $request)
    {
        $message = $request->input('message');

        if (!$message || strlen($message) < 10) {
            return response()->json(['reply' => 'Datele transmise nu sunt valide.'], 422);
        }

        $apiKey = env('GEMINI_API_KEY');
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $apiKey;

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [['text' => $message]]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Fără recomandări.';
                return response()->json(['reply' => $reply]);
            }

            return response()->json(['reply' => 'Eroare AI: ' . $response->body()], 500);
        } catch (\Exception $e) {
            return response()->json(['reply' => 'Eroare server: ' . $e->getMessage()], 500);
        }
    }
    public function analyzeExpenses($userId = null)
    {
        try {
            // Use authenticated user if no ID provided
            $userId = $userId ?? Auth::id();

            if (!$userId) {
                return response()->json(['reply' => 'You must be logged in to analyze expenses.'], 401);
            }

            // Get user's expenses
            $expenses = \App\Models\Expense::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

            if ($expenses->isEmpty()) {
                return response()->json(['reply' => 'No expense data found to analyze.']);
            }

            // Calculate basic statistics
            $total = $expenses->sum('amount');
            $totalExpenses = $expenses->where('amount', '<', 0)->sum('amount') * -1;
            $totalIncome = $expenses->where('amount', '>', 0)->sum('amount');

            // Group expenses (negative amounts) by category
            $expensesByCategory = $expenses
                ->where('amount', '<', 0)
                ->groupBy('category')
                ->map(function ($items, $category) use ($totalExpenses) {
                    $categoryTotal = $items->sum('amount') * -1; // Make positive for display
                    $percentage = $totalExpenses > 0 ? round(($categoryTotal / $totalExpenses) * 100, 2) : 0;
                    return [
                        'category' => $category,
                        'total' => $categoryTotal,
                        'percentage' => $percentage,
                        'transactions' => $items->count()
                    ];
                })
                ->sortByDesc('total')
                ->values();

            // Group income (positive amounts) by category
            $incomeByCategory = $expenses
                ->where('amount', '>', 0)
                ->groupBy('category')
                ->map(function ($items, $category) use ($totalIncome) {
                    $categoryTotal = $items->sum('amount');
                    $percentage = $totalIncome > 0 ? round(($categoryTotal / $totalIncome) * 100, 2) : 0;
                    return [
                        'category' => $category,
                        'total' => $categoryTotal,
                        'percentage' => $percentage,
                        'transactions' => $items->count()
                    ];
                })
                ->sortByDesc('total')
                ->values();

            // Format data for AI analysis
            $analysisData = [
                'totalBalance' => $total,
                'totalIncome' => $totalIncome,
                'totalExpenses' => $totalExpenses,
                'savingsRate' => $totalIncome > 0 ? round((($totalIncome - $totalExpenses) / $totalIncome) * 100, 2) : 0,
                'expenseCategories' => $expensesByCategory->toArray(),
                'incomeCategories' => $incomeByCategory->toArray(),
                'recentTransactions' => $expenses->take(5)->map(function($expense) {
                    return [
                        'title' => $expense->title,
                        'category' => $expense->category,
                        'amount' => $expense->amount,
                        'date' => $expense->created_at->format('Y-m-d')
                    ];
                })->toArray()
            ];

            $apiKey = env('GEMINI_API_KEY');
            $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $apiKey;

            $prompt = "As a financial advisor, analyze this user's expense data and provide personalized insights.
    Focus on:
    1. Category breakdown analysis (where they spend most and where their income comes from)
    2. Spending patterns and potential issues
    3. Specific recommendations to improve their financial habits
    4. Suggestions for better budget allocation
    5. Healthy financial habits they should adopt

    Format your response in clear sections with bullet points where appropriate. Do not use Markdown or characters for bold text like (**). Just give a simple answer with lines and normal paragraphs.

    Here's their financial data: " . json_encode($analysisData);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [['text' => $prompt]]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $analysis = $data['candidates'][0]['content']['parts'][0]['text'] ?? "Unable to generate expense analysis.";

                return [
                    'statistics' => $analysisData,
                    'insights' => $analysis,
                    'expenseCategories' => $expensesByCategory->toArray(),
                    'incomeCategories' => $incomeByCategory->toArray()
                ];
            } else {
                \Illuminate\Support\Facades\Log::error('AI Analysis Error', ['error' => $response->json()]);
                return [
                    'statistics' => $analysisData,
                    'insights' => 'Error generating AI insights. Please try again later.',
                    'expenseCategories' => $expensesByCategory->toArray(),
                    'incomeCategories' => $incomeByCategory->toArray()
                ];
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Expense Analysis Error', ['message' => $e->getMessage()]);
            return [
                'statistics' => [],
                'insights' => 'Error analyzing expenses: ' . $e->getMessage(),
                'expenseCategories' => [],
                'incomeCategories' => []
            ];
        }
    }

    public function expenseInsights()
    {
        $analysis = $this->analyzeExpenses();

        if (request()->wantsJson()) {
            return response()->json($analysis);
        }

        return view('expenses.insights', compact('analysis'));
    }
}
