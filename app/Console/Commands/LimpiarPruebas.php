<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\Conversation;
use App\Models\Lead;
use App\Models\Message;
use Illuminate\Console\Command;

/**
 * Borra los datos que dejan las pruebas, conservando los usuarios del panel.
 *
 * Existe para no tener que recordar el orden de borrado: las tablas se apuntan
 * entre si, asi que hacerlo al reves falla por las llaves foraneas.
 */
class LimpiarPruebas extends Command
{
    protected $signature = 'mavkora:limpiar-pruebas {--force : No pedir confirmacion}';

    protected $description = 'Borra leads, conversaciones, mensajes y citas de prueba';

    public function handle(): int
    {
        $resumen = [
            'leads' => Lead::count(),
            'conversaciones' => Conversation::count(),
            'mensajes' => Message::count(),
            'citas' => Appointment::count(),
        ];

        if (array_sum($resumen) === 0) {
            $this->info('No hay nada que borrar.');

            return self::SUCCESS;
        }

        $this->newLine();
        $this->line('  Se van a borrar:');
        foreach ($resumen as $tabla => $total) {
            $this->line(sprintf('    %-16s %d', $tabla, $total));
        }
        $this->newLine();
        $this->line('  <fg=gray>Los usuarios del panel NO se tocan.</>');
        $this->newLine();

        if (! $this->option('force') && ! $this->confirm('Continuar?', true)) {
            $this->comment('Cancelado.');

            return self::SUCCESS;
        }

        // El orden importa: primero lo que apunta a otras tablas.
        Message::query()->delete();
        Appointment::query()->delete();
        Conversation::query()->delete();
        Lead::query()->delete();

        $this->info('Listo. Datos de prueba borrados.');

        return self::SUCCESS;
    }
}
