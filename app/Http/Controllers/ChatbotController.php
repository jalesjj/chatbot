<?php
// app/Http/Controllers/ChatbotController.php (Simplified Fix)
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\ChatHistory;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class ChatbotController extends Controller
{
    private $client;
    private $apiKey;
    private $apiUrl;
    private $maxRetries = 3;
    private $retryDelay = 2; // seconds

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 60,
            'connect_timeout' => 30
        ]);
        $this->apiKey = env('GEMINI_API_KEY');
        $this->apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent';
    }

    public function index()
    {
        $user = Auth::user();
        
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

        if (!$this->apiKey) {
            return response()->json([
                'success' => false,
                'error' => 'API key tidak dikonfigurasi'
            ], 500);
        }

        try {
            // Untuk chat kedua dan seterusnya, buat context sederhana
            $messageWithContext = $request->message;
            
            if ($request->session_id) {
                $context = $this->buildSimpleContext($user, $request->session_id);
                if ($context) {
                    $messageWithContext = $context . "\n\nUser: " . $request->message;
                }
            }
            
            // Build request yang lebih sederhana
            $requestData = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $messageWithContext]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 2048,
                ]
            ];

            Log::info('Sending request to Gemini API', [
                'session_id' => $sessionId,
                'has_context' => $request->session_id ? true : false,
                'message_length' => strlen($messageWithContext)
            ]);

            // Try API call dengan retry logic
            $botResponse = $this->callGeminiApiWithRetry($requestData);
            
            if ($botResponse) {
                // Generate chat title untuk session baru
                $chatTitle = null;
                if (!$request->session_id) {
                    $chatTitle = $this->generateChatTitle($request->message);
                }
                
                // Simpan ke database
                $this->saveChatHistory($user->id, $sessionId, $request->message, $botResponse, $chatTitle);
                
                return response()->json([
                    'success' => true,
                    'response' => $botResponse,
                    'session_id' => $sessionId
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Gemini API tidak dapat diakses saat ini. Silakan coba beberapa saat lagi.'
                ], 503);
            }

        } catch (\Exception $e) {
            Log::error('General Error in sendMessage: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }

    private function callGeminiApiWithRetry($requestData)
    {
        $attempt = 0;
        
        while ($attempt < $this->maxRetries) {
            try {
                Log::info("Gemini API attempt " . ($attempt + 1) . "/" . $this->maxRetries);
                
                $response = $this->client->post($this->apiUrl . '?key=' . $this->apiKey, [
                    'json' => $requestData,
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'timeout' => 30 // Reduce timeout for retry
                ]);

                $data = json_decode($response->getBody(), true);
                
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    Log::info('Gemini API call successful on attempt ' . ($attempt + 1));
                    return $data['candidates'][0]['content']['parts'][0]['text'];
                } else {
                    Log::warning('Invalid Gemini API response structure: ' . json_encode($data));
                    
                    // Check if there's an error in the response
                    if (isset($data['error'])) {
                        Log::error('Gemini API Error: ' . json_encode($data['error']));
                        throw new \Exception('Gemini API Error: ' . $data['error']['message'] ?? 'Unknown error');
                    }
                    
                    throw new \Exception('Invalid API response structure');
                }

            } catch (ServerException $e) {
                $statusCode = $e->getResponse()->getStatusCode();
                $errorBody = $e->getResponse()->getBody()->getContents();
                
                Log::warning("Gemini API Server Error (Attempt " . ($attempt + 1) . "): {$statusCode} - {$errorBody}");
                
                // Server errors (5xx) are retryable
                if ($statusCode >= 500 && $statusCode < 600) {
                    $attempt++;
                    if ($attempt < $this->maxRetries) {
                        $delay = $this->retryDelay * pow(2, $attempt - 1);
                        Log::info("Retrying in {$delay} seconds...");
                        sleep($delay);
                        continue;
                    }
                }
                
                throw $e;
                
            } catch (ClientException $e) {
                $statusCode = $e->getResponse()->getStatusCode();
                $errorBody = $e->getResponse()->getBody()->getContents();
                
                Log::error("Gemini API Client Error: {$statusCode} - {$errorBody}");
                
                // Handle specific client errors
                if ($statusCode == 429) { // Rate limit
                    $attempt++;
                    if ($attempt < $this->maxRetries) {
                        $delay = $this->retryDelay * pow(2, $attempt - 1);
                        Log::info("Rate limited. Retrying in {$delay} seconds...");
                        sleep($delay);
                        continue;
                    }
                }
                
                // Don't retry on other client errors
                return null;
                
            } catch (\Exception $e) {
                Log::error("Gemini API General Error (Attempt " . ($attempt + 1) . "): " . $e->getMessage());
                
                $attempt++;
                if ($attempt < $this->maxRetries) {
                    $delay = $this->retryDelay * $attempt;
                    Log::info("Retrying in {$delay} seconds...");
                    sleep($delay);
                } else {
                    return null;
                }
            }
        }
        
        return null;
    }

    private function buildSimpleContext($user, $sessionId)
    {
        try {
            // Ambil 3 pesan terakhir untuk context
            $previousChats = ChatHistory::where('user_id', $user->id)
                ->where('session_id', $sessionId)
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get()
                ->reverse();
                
            if ($previousChats->isEmpty()) {
                return null;
            }
            
            $context = "Previous conversation:\n";
            foreach ($previousChats as $chat) {
                $context .= "User: " . $chat->user_message . "\n";
                $context .= "Assistant: " . $chat->bot_response . "\n";
            }
            $context .= "\nContinue the conversation:";
            
            return $context;
            
        } catch (\Exception $e) {
            Log::error('Error building context: ' . $e->getMessage());
            return null;
        }
    }

    private function saveChatHistory($userId, $sessionId, $userMessage, $botResponse, $chatTitle = null)
    {
        try {
            ChatHistory::create([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'user_message' => $userMessage,
                'bot_response' => $botResponse,
                'chat_title' => $chatTitle,
                'created_at' => now()
            ]);
            Log::info('Chat saved to database successfully');
        } catch (\Exception $dbError) {
            Log::error('Database save error: ' . $dbError->getMessage());
            // Don't throw, just log - we don't want to fail the response if DB save fails
        }
    }

    private function generateChatTitle($message)
    {
        $title = \Str::limit($message, 30);
        $title = preg_replace('/^(hai|halo|hello|hi|selamat|tolong|bisa|gimana|apa|bagaimana|jelaskan)\s+/i', '', $title);
        return ucfirst(trim($title));
    }

    // Load specific chat session
    public function loadChatSession(Request $request, $sessionId)
    {
        try {
            $user = Auth::user();
            $chats = ChatHistory::where('user_id', $user->id)
                ->where('session_id', $sessionId)
                ->orderBy('created_at', 'asc')
                ->get();
            
            return response()->json([
                'success' => true,
                'chats' => $chats,
                'session_id' => $sessionId
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading chat session: ' . $e->getMessage());
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

    // Method untuk check API status
    public function checkApiStatus()
    {
        try {
            $response = $this->client->get('https://generativelanguage.googleapis.com/v1beta/models?key=' . $this->apiKey, [
                'timeout' => 10
            ]);
            
            return response()->json([
                'success' => true,
                'status' => 'API is accessible'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 'API is not accessible: ' . $e->getMessage()
            ], 503);
        }
    }
}