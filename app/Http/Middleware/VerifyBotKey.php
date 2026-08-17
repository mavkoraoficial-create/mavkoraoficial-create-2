<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Protege la API del bot con una clave compartida con n8n.
 *
 * No se usa Sanctum ni OAuth porque del otro lado no hay un usuario: hay un
 * único servicio de confianza corriendo en la misma red de Docker. Una clave
 * comparada en tiempo constante es suficiente y no arrastra dependencias.
 */
class VerifyBotKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = config('mavkora.bot.api_key');

        // Sin clave configurada la API queda cerrada. Lo contrario —dejarla
        // abierta cuando falta la variable— es cómo se filtran las bases de datos.
        if (blank($expected)) {
            return response()->json([
                'message' => 'La API del bot no está configurada. Define BOT_API_KEY en el archivo .env.',
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $provided = (string) $request->header('X-Bot-Key', '');

        if (! hash_equals((string) $expected, $provided)) {
            return response()->json([
                'message' => 'Clave de bot inválida.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
