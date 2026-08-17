<?php

namespace App\Services\Bot;

/**
 * Arma el contexto que se le pasa a Claude cuando el cliente escribe libre.
 *
 * Todo sale de config/mavkora.php, que a su vez refleja lo que dice la landing.
 * Así el bot nunca contradice a la página web: si cambia un servicio, cambia
 * en los dos lados a la vez.
 */
class KnowledgeBase
{
    /**
     * Instrucciones de sistema para el modelo.
     *
     * Los límites importan tanto como la información: sin ellos el modelo
     * inventa precios y plazos, que es justo lo que no puede hacer un bot
     * comercial.
     */
    public function systemPrompt(): string
    {
        $company = config('mavkora.company');

        $lines = [
            "Eres el asistente virtual de {$company['name']}, una empresa de tecnología en {$company['location']}.",
            "Lema: {$company['tagline']}. {$company['description']}",
            '',
            'SERVICIOS QUE OFRECE LA EMPRESA:',
        ];

        foreach (config('mavkora.services') as $service) {
            $lines[] = "- {$service['name']}: {$service['summary']}";

            foreach ($service['details'] as $detail) {
                $lines[] = "  · {$detail}";
            }
        }

        $lines[] = '';
        $lines[] = 'CÓMO TRABAJAMOS (6 pasos):';

        foreach (config('mavkora.process') as $step) {
            $lines[] = "{$step['step']}. {$step['name']}: {$step['detail']}";
        }

        $lines[] = '';
        $lines[] = 'DATOS DE CONTACTO:';
        $lines[] = "- Correo: {$company['email']}";
        $lines[] = "- Teléfono: {$company['phone']}";
        $lines[] = "- Ubicación: {$company['location']}";
        $lines[] = '- Soporte técnico disponible 24/7';
        $lines[] = '- Horario comercial: '.$this->scheduleSentence();

        $lines[] = '';
        $lines[] = 'REGLAS QUE DEBES CUMPLIR SIEMPRE:';
        $lines[] = '1. Responde en español, de tú, cercano pero profesional.';
        $lines[] = '2. Sé breve: esto es WhatsApp. Máximo 4 líneas salvo que te pidan detalle.';
        $lines[] = '3. NUNCA des precios, tarifas ni plazos de entrega. Cada proyecto se cotiza según su alcance. Si te preguntan por precio, explica eso y ofrece tomar los datos para una cotización.';
        $lines[] = '4. NUNCA inventes servicios, tecnologías, clientes ni casos de éxito que no estén en la lista de arriba.';
        $lines[] = '5. Si no sabes algo o te piden algo fuera de tu alcance, dilo con naturalidad y ofrece pasar la conversación a un asesor.';
        $lines[] = '6. No prometas nada en nombre de la empresa: ni descuentos, ni fechas, ni condiciones contractuales.';
        $lines[] = '7. Formato de WhatsApp: usa *negrita* con un solo asterisco. No uses markdown, ni encabezados, ni tablas.';
        $lines[] = '8. Cierra ofreciendo el siguiente paso útil: ver servicios, pedir cotización, agendar reunión o hablar con un asesor.';
        $lines[] = '9. Si el cliente quiere cotizar o agendar, no le pidas los datos tú: dile que escriba *menu* para usar el asistente guiado, que es más rápido y no pierde información.';

        return implode("\n", $lines);
    }

    /**
     * Versión estructurada, útil para depurar desde n8n o el panel.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'company' => config('mavkora.company'),
            'services' => config('mavkora.services'),
            'process' => config('mavkora.process'),
            'schedule' => $this->scheduleSentence(),
            'system_prompt' => $this->systemPrompt(),
        ];
    }

    private function scheduleSentence(): string
    {
        $names = [1 => 'lunes', 2 => 'martes', 3 => 'miércoles', 4 => 'jueves', 5 => 'viernes', 6 => 'sábado', 7 => 'domingo'];
        $days = config('mavkora.schedule.days', [1, 2, 3, 4, 5]);

        $first = $names[reset($days)] ?? 'lunes';
        $last = $names[end($days)] ?? 'viernes';

        return sprintf(
            'de %s a %s, de %s a %s (hora de Colombia)',
            $first,
            $last,
            config('mavkora.schedule.start', '09:00'),
            config('mavkora.schedule.end', '17:00')
        );
    }
}
