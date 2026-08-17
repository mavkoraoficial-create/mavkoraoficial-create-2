<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Genera la clave compartida entre Laravel y n8n.
 *
 * Existe para que nadie caiga en la tentación de inventarse una clave corta
 * "mientras tanto": esa clave termina siendo la de producción.
 */
class GenerateBotKey extends Command
{
    protected $signature = 'mavkora:bot-key {--show : Solo mostrar la clave, sin tocar el archivo .env}';

    protected $description = 'Genera la clave de la API del chatbot (BOT_API_KEY) y la guarda en .env';

    public function handle(): int
    {
        $key = Str::random(48);

        if ($this->option('show')) {
            $this->line($key);

            return self::SUCCESS;
        }

        $path = base_path('.env');

        if (! file_exists($path)) {
            $this->error('No existe el archivo .env. Cópialo desde .env.example primero.');

            return self::FAILURE;
        }

        $contents = file_get_contents($path);

        $contents = preg_match('/^BOT_API_KEY=.*$/m', $contents)
            ? preg_replace('/^BOT_API_KEY=.*$/m', "BOT_API_KEY={$key}", $contents)
            : rtrim($contents, "\n")."\n\nBOT_API_KEY={$key}\n";

        file_put_contents($path, $contents);

        $this->info('Clave generada y guardada en .env');
        $this->newLine();
        $this->line("  BOT_API_KEY={$key}");
        $this->newLine();
        $this->comment('Copia este mismo valor en la credencial "Mavkora Bot API" de n8n.');
        $this->comment('Después ejecuta: php artisan config:clear');

        return self::SUCCESS;
    }
}
