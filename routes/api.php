<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelegramBotController;


Route::post('/webhook', [TelegramBotController::class, 'webhook']);
