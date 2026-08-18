<?php

namespace App\Console\Commands;

use App\Models\Conversation;
use App\Services\Bot\ConversationFlow;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Permite conversar con el bot desde la terminal, sin WhatsApp ni Meta de por medio.
 *
 * Dos modos:
 *   - Interactivo (por defecto): escribes tú y el bot responde.
 *   - Guion (--guion): corre una conversación completa sola. Sirve como prueba
 *     rápida para confirmar que nada se rompió después de tocar el flujo.
 */
class BotDemo extends Command
{
    protected $signature = 'mavkora:bot-demo
                            {--numero=573001112233 : Número que simula al cliente}
                            {--reset : Borra la conversación previa y empieza de cero}
                            {--guion : Corre una conversación de ejemplo sin preguntar nada}';

    protected $description = 'Conversa con el chatbot desde la terminal, sin conectar WhatsApp';

    /** Opciones de la última respuesta, para poder elegirlas por número. */
    private array $opciones = [];

    public function handle(ConversationFlow $flow): int
    {
        $numero = (string) $this->option('numero');

        if ($this->option('reset') || $this->option('guion')) {
            // El guion siempre parte de cero: si arrancara a mitad de un
            // formulario anterior, la demostración no tendría sentido.
            Conversation::where('wa_id', $numero)->delete();
        }

        return $this->option('guion')
            ? $this->correrGuion($flow, $numero)
            : $this->modoInteractivo($flow, $numero);
    }

    /*
    |--------------------------------------------------------------------------
    | Modo guion
    |--------------------------------------------------------------------------
    */

    private function correrGuion(ConversationFlow $flow, string $numero): int
    {
        $this->titulo('CONVERSACION DE EJEMPLO', "Cliente simulado: +{$numero}");

        $guion = [
            ['seccion' => '1. Pregunta libre: la responde la base de conocimiento, sin IA y sin costo'],
            ['texto' => 'cuanto cuesta una pagina web?'],
            ['texto' => 'y cuanto se demoran en entregar?'],

            ['seccion' => '2. Menu principal'],
            ['texto' => 'menu'],

            ['seccion' => '3. Cotizacion completa: al final se crea el lead'],
            ['tocar' => 'menu_quote'],
            ['tocar' => 'qsvc_web'],
            ['texto' => 'Mauro Vargas'],
            ['texto' => 'mauro@mavkora.com'],
            ['texto' => 'Necesito una tienda en linea para vender ropa, con pagos y envios'],

            ['seccion' => '4. Agendar la reunion inicial'],
            ['tocar' => 'menu_appointment'],
            ['tocar' => '__PRIMERA_OPCION__'],

            ['seccion' => '5. Escalar a una persona: el bot se calla'],
            ['texto' => 'necesito hablar con un asesor'],
            ['texto' => 'sigue ahi alguien?'],
        ];

        foreach ($guion as $paso) {
            if (isset($paso['seccion'])) {
                $this->newLine();
                $this->line('  <bg=blue;fg=white> '.$paso['seccion'].' </>');

                continue;
            }

            $replyId = null;
            $texto = $paso['texto'] ?? '';

            if (isset($paso['tocar'])) {
                $elegida = $paso['tocar'] === '__PRIMERA_OPCION__'
                    ? ($this->opciones[0] ?? null)
                    : collect($this->opciones)->firstWhere('id', $paso['tocar']);

                if ($elegida === null) {
                    $this->error("  No se encontro la opcion «{$paso['tocar']}» en la respuesta anterior.");

                    return self::FAILURE;
                }

                $replyId = $elegida['id'];
                $texto = $elegida['title'];
            }

            $this->enviar($flow, $numero, $texto, $replyId, tocado: $replyId !== null);
        }

        $this->newLine();
        $this->line('  <fg=green;options=bold>Fin del guion.</> Los datos quedaron guardados:');
        $this->line('    Leads          -> http://localhost:8000/admin/leads');
        $this->line('    Conversaciones -> http://localhost:8000/admin/conversaciones');
        $this->line('    Citas          -> http://localhost:8000/admin/citas');
        $this->newLine();

        return self::SUCCESS;
    }

    /*
    |--------------------------------------------------------------------------
    | Modo interactivo
    |--------------------------------------------------------------------------
    */

    private function modoInteractivo(ConversationFlow $flow, string $numero): int
    {
        $this->titulo('MODO INTERACTIVO', "Simulando al cliente +{$numero}");
        $this->line('  Escribe como si fueras el cliente. Para elegir una opcion del menu');
        $this->line('  puedes escribir su <fg=cyan>numero</> o el texto de la opcion.');
        $this->line('  Escribe <fg=red>salir</> para terminar.');
        $this->newLine();
        $this->line(str_repeat('-', 68));

        while (true) {
            $entrada = trim((string) $this->ask('  Tu'));

            if ($entrada === '') {
                continue;
            }

            if (in_array(Str::lower($entrada), ['salir', 'exit', 'quit'], true)) {
                $this->newLine();
                $this->info('  Listo. La conversacion quedo guardada.');
                $this->line('  Mira el hilo en http://localhost:8000/admin/conversaciones');
                $this->newLine();

                return self::SUCCESS;
            }

            // Si escribio un numero y la respuesta anterior traia opciones,
            // se traduce a esa eleccion: equivale a tocar el boton en WhatsApp.
            $replyId = null;
            $tocado = false;

            if (ctype_digit($entrada) && isset($this->opciones[(int) $entrada - 1])) {
                $elegida = $this->opciones[(int) $entrada - 1];
                $replyId = $elegida['id'];
                $entrada = $elegida['title'];
                $tocado = true;
            }

            $this->enviar($flow, $numero, $entrada, $replyId, $tocado);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Envio y presentacion
    |--------------------------------------------------------------------------
    */

    private function enviar(ConversationFlow $flow, string $numero, string $texto, ?string $replyId, bool $tocado): void
    {
        $etiqueta = $tocado ? "[toca] {$texto}" : $texto;
        $this->newLine();
        $this->line("  <fg=yellow;options=bold>CLIENTE</> | {$etiqueta}");

        $resultado = $flow->handle([
            'wa_id' => $numero,
            'profile_name' => 'Mauro Vargas',
            // Unico por mensaje: si se repitiera, el bot lo tomaria por un
            // reintento de Meta y no responderia.
            'wa_message_id' => 'demo.'.Str::uuid(),
            'type' => $replyId ? 'interactive' : 'text',
            'text' => $texto,
            'reply_id' => $replyId,
        ]);

        $this->mostrarRespuesta($resultado);
    }

    /**
     * @param  array<string, mixed>  $resultado
     */
    private function mostrarRespuesta(array $resultado): void
    {
        $reply = $resultado['reply'] ?? [];
        $kind = $reply['kind'] ?? 'none';
        $this->opciones = [];

        if ($resultado['needs_ai'] ?? false) {
            $this->line('  <fg=magenta>BOT</>     | [aqui pediria respuesta a la IA]');
            $this->line('  <fg=gray>            Con BOT_AI_ENABLED=false esto no deberia aparecer.</>');

            return;
        }

        if ($kind === 'none') {
            $this->line('  <fg=gray>BOT     | (silencio: un asesor tiene la conversacion)</>');
        } else {
            foreach (explode("\n", (string) ($reply['body'] ?? '')) as $linea) {
                $this->line('  <fg=green;options=bold>BOT</>     | '.$linea);
            }

            if ($kind === 'buttons') {
                $this->opciones = $reply['buttons'] ?? [];
            } elseif ($kind === 'list') {
                foreach ($reply['sections'] ?? [] as $seccion) {
                    foreach ($seccion['rows'] ?? [] as $fila) {
                        $this->opciones[] = $fila;
                    }
                }
            }

            foreach ($this->opciones as $i => $opcion) {
                $n = $i + 1;
                $desc = isset($opcion['description']) ? " <fg=gray>- {$opcion['description']}</>" : '';
                $this->line("          |   <fg=cyan>[{$n}]</> {$opcion['title']}{$desc}");
            }
        }

        if ($aviso = $resultado['notify'] ?? null) {
            $this->line("  <fg=magenta;options=bold>AVISO</>   | se notificaria al equipo: {$aviso['type']}");
        }

        $this->line('  <fg=gray>          paso: '.($resultado['step'] ?? '?').'</>');
    }

    private function titulo(string $titulo, string $subtitulo): void
    {
        $this->newLine();
        $this->line('  <fg=green;options=bold>Chatbot de Mavkora</> · '.$titulo);
        $this->line("  <fg=gray>{$subtitulo}</>");
        $this->newLine();
    }
}
