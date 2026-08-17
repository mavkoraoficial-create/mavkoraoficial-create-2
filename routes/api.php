<?php

use App\Http\Controllers\Api\BotController;
use App\Http\Middleware\VerifyBotKey;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API del chatbot
|--------------------------------------------------------------------------
|
| Solo la consume n8n, desde la red interna de Docker. Toda la lógica de
| conversación vive en App\Services\Bot\ConversationFlow: estas rutas son
| apenas la puerta de entrada.
|
| Autenticación: cabecera X-Bot-Key con el valor de BOT_API_KEY.
|
*/

Route::middleware([VerifyBotKey::class, 'throttle:120,1'])
    ->prefix('bot')
    ->name('api.bot.')
    ->group(function () {
        // Comprobación rápida de que n8n llegó bien a Laravel.
        Route::get('/health', [BotController::class, 'health'])->name('health');

        // Base de conocimiento; útil para depurar el prompt desde n8n.
        Route::get('/knowledge', [BotController::class, 'knowledge'])->name('knowledge');

        // El endpoint importante: mensaje entrante -> respuesta a enviar.
        Route::post('/incoming', [BotController::class, 'incoming'])->name('incoming');

        // n8n reporta lo que efectivamente envió, para tener el hilo completo.
        Route::post('/sent', [BotController::class, 'sent'])->name('sent');
    });
