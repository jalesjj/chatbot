<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Auth;

// Auth routes (manual, bukan Breeze)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Chatbot routes (protected) - MIDDLEWARE ADA DI SINI
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [ChatbotController::class, 'index'])->name('chatbot.index');
    Route::post('/chat', [ChatbotController::class, 'sendMessage'])->name('chatbot.send');
    Route::delete('/chat/clear', [ChatbotController::class, 'clearHistory'])->name('chatbot.clear');
    Route::get('/chat/history', [ChatbotController::class, 'getHistory'])->name('chatbot.history');
});

// Landing page
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('chatbot.index');
    }
    return redirect()->route('login');
})->name('home');