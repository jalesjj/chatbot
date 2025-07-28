<?php

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
        
        // Ambil chat history jika ada
        try {
            $chatHistory = $user->getRecentChats(50);
        } catch (\Exception $e) {
            Log::error('Error loading chat history: ' . $e->getMessage());
            $chatHistory = collect();
        }
        
        return view('chatbot', compact('chatHistory'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000'
        ]);

        $user = Auth::user();
        $sessionId = $request->session()->getId();

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
            // SIMPLE REQUEST tanpa context dulu
            $requestData = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $request->message]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 2048,
                ]
            ];

            Log::info('Sending request to Gemini API');
            Log::info('Request data: ' . json_encode($requestData));

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
                
                // Simpan ke database
                try {
                    ChatHistory::create([
                        'user_id' => $user->id,
                        'session_id' => $sessionId,
                        'user_message' => $request->message,
                        'bot_response' => $botResponse,
                        'created_at' => now()
                    ]);
                    Log::info('Chat saved to database successfully');
                } catch (\Exception $dbError) {
                    Log::error('Database save error: ' . $dbError->getMessage());
                    Log::error('Stack trace: ' . $dbError->getTraceAsString());
                    // Continue tanpa error, chat tetap bisa jalan
                }
                
                return response()->json([
                    'success' => true,
                    'response' => $botResponse
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

    // Clear history
    public function clearHistory(Request $request)
    {
        try {
            $user = Auth::user();
            ChatHistory::where('user_id', $user->id)->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'History berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal menghapus history'
            ], 500);
        }
    }

    // Get history
    public function getHistory(Request $request)
    {
        try {
            $user = Auth::user();
            $history = $user->getRecentChats(100);
            
            return response()->json([
                'success' => true,
                'history' => $history
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => true,
                'history' => []
            ]);
        }
    }
}