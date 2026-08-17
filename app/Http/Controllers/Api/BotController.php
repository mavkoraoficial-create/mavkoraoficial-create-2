<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\Bot\ConversationFlow;
use App\Services\Bot\KnowledgeBase;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Puerta de entrada de n8n hacia el chatbot.
 *
 * Deliberadamente delgado: valida, delega y devuelve. Si hay que cambiar cómo
 * responde el bot, se cambia ConversationFlow, no este archivo.
 */
class BotController extends Controller
{
    public function health(): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'service' => config('mavkora.company.name').' bot API',
            'ai_enabled' => (bool) config('mavkora.bot.ai_enabled'),
            'time' => now()->toIso8601String(),
        ]);
    }

    public function knowledge(KnowledgeBase $knowledge): JsonResponse
    {
        return response()->json($knowledge->toArray());
    }

    /**
     * Mensaje entrante ya normalizado por n8n.
     *
     * Devuelve qué responder y, si el texto no encajó en ningún paso del menú,
     * el contexto para que n8n le pregunte a Claude.
     */
    public function incoming(Request $request, ConversationFlow $flow): JsonResponse
    {
        $data = $request->validate([
            'wa_id' => ['required', 'string', 'max:32'],
            'profile_name' => ['nullable', 'string', 'max:255'],
            'wa_message_id' => ['nullable', 'string', 'max:191'],
            'type' => ['nullable', 'string', 'max:32'],
            'text' => ['nullable', 'string', 'max:4096'],
            'reply_id' => ['nullable', 'string', 'max:255'],
        ]);

        return response()->json($flow->handle($data));
    }

    /**
     * Registra un mensaje saliente para que el historial del panel quede completo.
     *
     * Se llama después de enviar a Meta, así que un fallo aquí no debe romper el
     * flujo: el cliente ya recibió su respuesta.
     */
    public function sent(Request $request): JsonResponse
    {
        $data = $request->validate([
            'conversation_id' => ['required', 'integer', 'exists:conversations,id'],
            'wa_message_id' => ['nullable', 'string', 'max:191'],
            'type' => ['nullable', 'string', 'max:32'],
            'body' => ['nullable', 'string', 'max:8192'],
            'generated_by' => ['nullable', 'string', 'in:flow,ai,human'],
            'payload' => ['nullable', 'array'],
        ]);

        $conversation = Conversation::findOrFail($data['conversation_id']);

        try {
            $message = $conversation->messages()->create([
                'wa_message_id' => $data['wa_message_id'] ?? null,
                'direction' => Message::OUT,
                'type' => $data['type'] ?? 'text',
                'body' => $data['body'] ?? null,
                'payload' => $data['payload'] ?? null,
                'generated_by' => $data['generated_by'] ?? 'flow',
            ]);
        } catch (QueryException $e) {
            // Mismo mensaje reportado dos veces: no es un error que valga la pena
            // propagar a n8n, el registro ya existe.
            if ($e->getCode() === '23000') {
                return response()->json(['ok' => true, 'duplicate' => true]);
            }

            throw $e;
        }

        $conversation->forceFill(['last_message_at' => now()])->save();

        return response()->json(['ok' => true, 'message_id' => $message->id]);
    }
}
