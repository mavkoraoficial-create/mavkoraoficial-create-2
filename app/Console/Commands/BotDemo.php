<?php

namespace App\Console\Commands;

use App\Models\Conversation;
use App\Services\Bot\ConversationFlow;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Permite conversar con el bot desde la terminal, sin WhatsApp ni Meta de por medio.
 *
 * Es la forma más rápida de probar un cambio en el flujo: no hay que reenviar
 * mensajes desde el celular ni esperar a que Meta entregue el webhook.
 */
class BotDemo extends Command
{
    protected $signature = 'mavkora:bot-demo
                            {--numero=573001112233 : Número que simula al cliente}
                            {--reset : Borra la conversación previa y empieza de cero}';

    protected $description = 'Conversa con el chatbot desde la terminal, sin conectar WhatsApp';

    public function handle(ConversationFlow $flow): int
    {
        $numero = (string) $this->option('numero');

        if ($this->option('reset')) {
            Conversation::where('wa_id', $numero)->delete();
            $this->comment("Conversación de {$numero} borrada.");
        }

        $this->newLine();
        $this->line('  <fg=green;options=bold>Chatbot de Mavkora</> — modo de prueba');
        $this->line("  Simulando al cliente <fg=yellow>+{$numero}</>");
        $this->newLine();
        $this->line('  Escribe como si fueras el cliente. Para elegir una opción del menú');
        $this->line('  puedes escribir su <fg=cyan>número</> o el texto de la opción.');
        $this->line('  Escribe <fg=red>salir</> para terminar.');
        $this->newLine();
        $this->line(str_repeat('─', 64));

        // Guarda las opciones de la última respuesta para poder elegirlas por número.
        $opciones = [];

        while (true) {
            $entrada = trim((string) $this->ask('  Tú'));

            if ($entrada === '' ) {
                continue;
            }

            if (in_array(Str::lower($entrada), ['salir', 'exit', 'quit'], true)) {
                $this->newLine();
                $this->info('  Listo. La conversación quedó guardada en la base de datos.');
                $this->line("  Míra el hilo completo en: /admin/conversaciones");
                $this->newLine();

                return self::SUCCESS;
            }

            // Si escribió un número y la respuesta anterior tenía opciones, se
            // traduce a la elección correspondiente, igual que tocar el botón.
            $replyId = null;
            if (ctype_digit($entrada) && isset($opciones[(int) $entrada - 1])) {
                $elegida = $opciones[(int) $entrada - 1];
                $replyId = $elegida['id'];
                $entrada = $elegida['title'];
                $this->line("  <fg=gray>(equivale a tocar «{$entrada}»)</>");
            }

            $resultado = $flow->handle([
                'wa_id' => $numero,
                'profile_name' => 'Cliente de Prueba',
                // Único por mensaje: si se repitiera, el bot lo tomaría por un
                // reintento de Meta y no respondería.
                'wa_message_id' => 'demo.'.Str::uuid(),
                'type' => $replyId ? 'interactive' : 'text',
                'text' => $entrada,
                'reply_id' => $replyId,
            ]);

            $opciones = $this->mostrarRespuesta($resultado);
        }
    }

    /**
     * Pinta la respuesta del bot y devuelve sus opciones, para poder elegirlas
     * por número en el siguiente turno.
     *
     * @param  array<string, mixed>  $resultado
     * @return list<array{id: string, title: string}>
     */
    private function mostrarRespuesta(array $resultado): array
    {
        $reply = $resultado['reply'] ?? [];
        $kind = $reply['kind'] ?? 'none';

        $this->newLine();

        if ($resultado['needs_ai'] ?? false) {
            $this->line('  <fg=magenta>[El bot pediría respuesta a la IA]</>');
            $this->line('  <fg=gray>Con BOT_AI_ENABLED=false esto no debería aparecer.</>');
            $this->newLine();

            return [];
        }

        if ($kind === 'none') {
            $this->line('  <fg=gray>[El bot guarda silencio: un asesor tiene la conversación]</>');
            $this->newLine();

            return [];
        }

        foreach (explode("\n", (string) ($reply['body'] ?? '')) as $linea) {
            $this->line('  <fg=green>Bot</> │ '.$linea);
        }

        $opciones = [];

        if ($kind === 'buttons') {
            $opciones = $reply['buttons'] ?? [];
        } elseif ($kind === 'list') {
            foreach ($reply['sections'] ?? [] as $seccion) {
                foreach ($seccion['rows'] ?? [] as $fila) {
                    $opciones[] = $fila;
                }
            }
        }

        if ($opciones !== []) {
            $this->newLine();
            foreach ($opciones as $i => $opcion) {
                $numero = $i + 1;
                $desc = isset($opcion['description']) ? " <fg=gray>— {$opcion['description']}</>" : '';
                $this->line("      <fg=cyan>[{$numero}]</> {$opcion['title']}{$desc}");
            }
        }

        if ($aviso = $resultado['notify'] ?? null) {
            $this->newLine();
            $this->line("  <fg=yellow>⚑ Se avisaría al equipo: {$aviso['type']}</>");
        }

        $this->newLine();
        $this->line('  <fg=gray>paso: '.($resultado['step'] ?? '?').'</>');
        $this->line(str_repeat('─', 64));

        return $opciones;
    }
}
