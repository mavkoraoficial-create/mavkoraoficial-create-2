<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeadController extends Controller
{
    /**
     * Recibe el formulario de cotización de la landing.
     *
     * Responde JSON porque el formulario vive dentro de un modal: recargar la
     * página lo cerraría y el visitante no vería la confirmación.
     */
    public function store(Request $request): JsonResponse
    {
        // Trampa para bots: un campo que un humano nunca ve ni llena. Si viene
        // con contenido, se acepta en silencio sin guardar nada, para no darle
        // al spammer la señal de que fue detectado.
        if (filled($request->input('website'))) {
            return response()->json(['ok' => true, 'message' => 'Solicitud recibida.']);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'max:32'],
            'service' => ['required', 'string', Rule::in(array_keys(config('mavkora.services')))],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ], [
            'name.required' => 'Necesitamos tu nombre para contactarte.',
            'email.required' => 'Necesitamos un correo para enviarte la propuesta.',
            'email.email' => 'Ese correo no parece válido.',
            'service.required' => 'Selecciona el servicio que te interesa.',
            'service.in' => 'El servicio seleccionado no es válido.',
            'message.required' => 'Cuéntanos brevemente qué necesitas.',
            'message.min' => 'Cuéntanos un poco más para poder cotizarte bien.',
        ]);

        $lead = Lead::create([
            ...$data,
            'source' => 'web',
            'status' => 'new',
        ]);

        return response()->json([
            'ok' => true,
            'message' => '¡Gracias, '.strtok($lead->name, ' ').'! Recibimos tu solicitud y te contactaremos muy pronto.',
        ], 201);
    }
}
