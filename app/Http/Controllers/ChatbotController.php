<?php
// app/Http/Controllers/ChatbotController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\ChatHistory;


class ChatbotController extends Controller
{
    private $client;
    private $apiKey;
    private $apiUrl;

    public function __construct()
    {
        $this->client = new Client(['timeout' => 30]);
        $this->apiKey = env('GEMINI_API_KEY');
        $this->apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent';
    }

    public function index()
    {
        $user = Auth::user();
        
        // Ambil chat sessions (bukan individual messages)
        try {
            $chatSessions = $user->getChatSessions(50);
        } catch (\Exception $e) {
            Log::error('Error loading chat sessions: ' . $e->getMessage());
            $chatSessions = collect();
        }
        
        return view('chatbot', compact('chatSessions'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'session_id' => 'nullable|string'
        ]);

        $user = Auth::user();
        $sessionId = $request->session_id ?: $request->session()->getId() . '_' . time();

        // Debug: Log API key
        Log::info('API Key exists: ' . ($this->apiKey ? 'Yes' : 'No'));
        Log::info('User message: ' . $request->message);

        if (!$this->apiKey) {
            return response()->json([
                'success' => false,
                'error' => 'API key tidak dikonfigurasi'
            ], 500);
        }

        try {
            // Get context dari session jika ada
            $context = [];
            if ($request->session_id) {
                $previousChats = $user->getChatBySession($request->session_id)->take(-5); // 5 chat terakhir untuk context
                foreach ($previousChats as $chat) {
                    $context[] = [
                        'parts' => [['text' => $chat->user_message]]
                    ];
                    $context[] = [
                        'parts' => [['text' => $chat->bot_response]]
                    ];
                }
            }

            // Build request dengan context
            $requestData = [
                'contents' => array_merge($context, [
                    [
                        'parts' => [
                            ['text' => $request->message]
                        ]
                    ]
                ]),
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 2048,
                ]
            ];

            Log::info('Sending request to Gemini API');

            $response = $this->client->post($this->apiUrl . '?key=' . $this->apiKey, [
                'json' => $requestData,
                'headers' => [
                    'Content-Type' => 'application/json',
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            
            Log::info('Gemini API Response: ' . json_encode($data));
            
            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                $botResponse = $data['candidates'][0]['content']['parts'][0]['text'];
                
                // Generate chat title untuk session baru
                $chatTitle = null;
                if (!$request->session_id) {
                    $chatTitle = $this->generateChatTitle($request->message);
                }
                
                // Simpan ke database
                try {
                    ChatHistory::create([
                        'user_id' => $user->id,
                        'session_id' => $sessionId,
                        'user_message' => $request->message,
                        'bot_response' => $botResponse,
                        'chat_title' => $chatTitle,
                        'created_at' => now()
                    ]);
                    Log::info('Chat saved to database successfully');
                } catch (\Exception $dbError) {
                    Log::error('Database save error: ' . $dbError->getMessage());
                    Log::error('Stack trace: ' . $dbError->getTraceAsString());
                }
                
                return response()->json([
                    'success' => true,
                    'response' => $botResponse,
                    'session_id' => $sessionId
                ]);
            } else {
                Log::error('Invalid Gemini API response structure: ' . json_encode($data));
                return response()->json([
                    'success' => false,
                    'error' => 'Respons tidak valid dari Gemini API'
                ], 500);
            }

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $errorBody = $e->getResponse()->getBody()->getContents();
            
            Log::error('Gemini API Client Error: ', [
                'status' => $statusCode,
                'body' => $errorBody,
                'url' => $this->apiUrl
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'API Error: ' . $statusCode . ' - ' . $errorBody
            ], 500);
            
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            Log::error('Gemini API Request Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Koneksi ke API bermasalah: ' . $e->getMessage()
            ], 500);
            
        } catch (\Exception $e) {
            Log::error('General Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'error' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // Generate chat title dari first message
    private function generateChatTitle($message)
    {
        $title = \Str::limit($message, 30);
        // Remove common starting words
        $title = preg_replace('/^(hai|halo|hello|hi|selamat|tolong|bisa|gimana|apa|bagaimana|jelaskan)\s+/i', '', $title);
        return ucfirst(trim($title));
    }

    // Load specific chat session
    public function loadChatSession(Request $request, $sessionId)
    {
        try {
            $user = Auth::user();
            $chats = $user->getChatBySession($sessionId);
            
            return response()->json([
                'success' => true,
                'chats' => $chats,
                'session_id' => $sessionId
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal memuat chat session'
            ], 500);
        }
    }

    // Update chat title
    public function updateChatTitle(Request $request, $sessionId)
    {
        $request->validate([
            'title' => 'required|string|max:100'
        ]);

        try {
            $user = Auth::user();
            
            // Update title di semua chat dalam session ini
            ChatHistory::where('user_id', $user->id)
                ->where('session_id', $sessionId)
                ->update(['chat_title' => $request->title]);
            
            return response()->json([
                'success' => true,
                'message' => 'Judul chat berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal mengupdate judul'
            ], 500);
        }
    }

    // Delete specific chat session
    public function deleteChatSession(Request $request, $sessionId)
    {
        try {
            $user = Auth::user();
            
            ChatHistory::where('user_id', $user->id)
                ->where('session_id', $sessionId)
                ->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Chat berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal menghapus chat'
            ], 500);
        }
    }

    // Get chat sessions untuk sidebar
    public function getChatSessions(Request $request)
    {
        try {
            $user = Auth::user();
            $sessions = $user->getChatSessions(100);
            
            return response()->json([
                'success' => true,
                'sessions' => $sessions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => true,
                'sessions' => []
            ]);
        }
    }

    // Get history (backward compatibility)
    public function getHistory(Request $request)
    {
        return $this->getChatSessions($request);
    }
}