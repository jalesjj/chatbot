<?php
// routes/web.php (Updated)
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;

// Auth routes (manual, bukan Breeze)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Test dengan full class path dulu
Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class, \App\Http\Middleware\CheckBannedUser::class])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/users/{id}', [AdminController::class, 'userDetail'])->name('admin.user.detail');
    Route::post('/users/{id}/ban', [AdminController::class, 'banUser'])->name('admin.user.ban');
    Route::post('/users/{id}/unban', [AdminController::class, 'unbanUser'])->name('admin.user.unban');
    Route::put('/users/{id}/role', [AdminController::class, 'changeRole'])->name('admin.user.role');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.user.delete');
    Route::delete('/users/{id}/chats', [AdminController::class, 'deleteUserChats'])->name('admin.user.delete.chats');
});

// Fix chatbot routes juga (ganti checkbanned jadi check.banned)
Route::middleware(['auth', \App\Http\Middleware\CheckBannedUser::class])->group(function () {
    Route::get('/dashboard', [ChatbotController::class, 'index'])->name('chatbot.index');
    Route::post('/chat', [ChatbotController::class, 'sendMessage'])->name('chatbot.send');
    Route::get('/chat/sessions', [ChatbotController::class, 'getChatSessions'])->name('chatbot.sessions');
    Route::get('/chat/session/{sessionId}', [ChatbotController::class, 'loadChatSession'])->name('chatbot.session');
    Route::put('/chat/session/{sessionId}/title', [ChatbotController::class, 'updateChatTitle'])->name('chatbot.update.title');
    Route::delete('/chat/session/{sessionId}', [ChatbotController::class, 'deleteChatSession'])->name('chatbot.delete.session');
    Route::get('/chat/history', [ChatbotController::class, 'getHistory'])->name('chatbot.history');
});

// Landing page
Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('chatbot.index');
    }
    return redirect()->route('login');
})->name('home');