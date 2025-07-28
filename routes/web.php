<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatbotController;

Route::get('/', [ChatbotController::class, 'index']);
Route::post('/chat', [ChatbotController::class, 'sendMessage']);