<?php
//namespace App\Http\Controllers;
//
//use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Http;
//use Illuminate\Support\Facades\Log;
//
//class ChatbotController extends Controller
//{
//    public function show()
//    {
//        return view('chatbot.chatbot');
//    }
//
//    public function talk(Request $request)
//    {
//        try {
//            $userMessage = $request->input('message');
//            $apiKey = env('GEMINI_API_KEY');
//
//            $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $apiKey;
//
//            $response = Http::withHeaders([
//                'Content-Type' => 'application/json',
//            ])->post($url, [
//                'contents' => [
//                    [
//                        'role' => 'user',
//                        'parts' => [
//                            [
//                                'text' => "You are a financial assistant. Always respond with a focus on financial literacy, economics, budgeting, investments, or financial planning unless otherwise asked. Be accurate and use professional finance terminology when appropriate."
//                            ]
//                        ]
//                    ],
//                    [
//                        'role' => 'user',
//                        'parts' => [
//                            ['text' => $userMessage]
//                        ]
//                    ]
//                ]
//            ]);
//
//
//            Log::info('Gemini API Response', ['status' => $response->status(), 'body' => $response->body()]);
//
//            if ($response->successful()) {
//                $data = $response->json();
//                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? "Sorry, no response.";
//                return response()->json(['reply' => $reply]);
//            } else {
//                return response()->json([
//                    'reply' => 'Error: ' . ($response->json()['error']['message'] ?? 'Unknown error'),
//                ], 200);
//            }
//        } catch (\Exception $e) {
//            Log::error('Chatbot Error', ['message' => $e->getMessage()]);
//            return response()->json(['reply' => 'Server error: ' . $e->getMessage()], 200);
//        }
//
//    }
//}


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
                            'text' => "You are a financial assistant. Always respond with a focus on financial literacy, economics, budgeting, investments, or financial planning unless otherwise asked. Be accurate and use professional finance terminology when appropriate."
                        ]
                    ]
                ]
            ];

            // Add message history to provide context if available
            if (!empty($messageHistory)) {
                $contents = array_merge($contents, $messageHistory);
            } else {
                // If no history, just add the current message
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
}
