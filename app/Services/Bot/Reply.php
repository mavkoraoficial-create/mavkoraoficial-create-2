<?php

namespace App\Services\Bot;

use Illuminate\Support\Str;

/**
 * Construye la respuesta que n8n traducirá al formato de la Cloud API.
 *
 * El único motivo por el que esta clase existe es que Meta rechaza el mensaje
 * completo con un 400 si un título se pasa por un carácter. Recortar aquí, en
 * un solo lugar, evita que un servicio con nombre largo tumbe la respuesta.
 *
 * Límites de la WhatsApp Cloud API (a la fecha, API v21):
 *   cuerpo 1024 · encabezado 60 · pie 60
 *   botón de respuesta 20 · máximo 3 botones
 *   fila de lista: título 24, descripción 72 · máximo 10 filas en total
 */
class Reply
{
    private const BODY_MAX = 1024;
    private const HEADER_MAX = 60;
    private const FOOTER_MAX = 60;
    private const BUTTON_MAX = 20;
    private const ROW_TITLE_MAX = 24;
    private const ROW_DESC_MAX = 72;
    private const MAX_BUTTONS = 3;
    private const MAX_ROWS = 10;

    /**
     * Mensaje de texto plano.
     *
     * @return array<string, mixed>
     */
    public static function text(string $body): array
    {
        return [
            'kind' => 'text',
            'body' => self::cut($body, self::BODY_MAX),
        ];
    }

    /**
     * Hasta tres botones de respuesta rápida.
     *
     * @param  list<array{id: string, title: string}>  $buttons
     * @return array<string, mixed>
     */
    public static function buttons(string $body, array $buttons, ?string $footer = null): array
    {
        $buttons = array_slice($buttons, 0, self::MAX_BUTTONS);

        return [
            'kind' => 'buttons',
            'body' => self::cut($body, self::BODY_MAX),
            'footer' => $footer ? self::cut($footer, self::FOOTER_MAX) : null,
            'buttons' => array_map(fn (array $b) => [
                'id' => $b['id'],
                'title' => self::cut($b['title'], self::BUTTON_MAX),
            ], $buttons),
        ];
    }

    /**
     * Lista desplegable. Se usa cuando hay más de tres opciones.
     *
     * @param  list<array{title?: string, rows: list<array{id: string, title: string, description?: string}>}>  $sections
     * @return array<string, mixed>
     */
    public static function list(string $body, string $buttonLabel, array $sections, ?string $footer = null): array
    {
        // El tope de 10 filas es global, no por sección: hay que contarlas
        // en conjunto y cortar donde toque.
        $remaining = self::MAX_ROWS;
        $trimmed = [];

        foreach ($sections as $section) {
            if ($remaining <= 0) {
                break;
            }

            $rows = array_slice($section['rows'], 0, $remaining);
            $remaining -= count($rows);

            $trimmed[] = [
                'title' => isset($section['title']) ? self::cut($section['title'], self::ROW_TITLE_MAX) : null,
                'rows' => array_map(fn (array $r) => array_filter([
                    'id' => $r['id'],
                    'title' => self::cut($r['title'], self::ROW_TITLE_MAX),
                    'description' => isset($r['description'])
                        ? self::cut($r['description'], self::ROW_DESC_MAX)
                        : null,
                ], fn ($v) => $v !== null), $rows),
            ];
        }

        return [
            'kind' => 'list',
            'body' => self::cut($body, self::BODY_MAX),
            'button' => self::cut($buttonLabel, self::BUTTON_MAX),
            'footer' => $footer ? self::cut($footer, self::FOOTER_MAX) : null,
            'sections' => $trimmed,
        ];
    }

    /**
     * Marca que no hay nada que responder. Se usa cuando un asesor humano ya
     * tomó la conversación y el bot debe quedarse callado.
     *
     * @return array<string, mixed>
     */
    public static function silence(): array
    {
        return ['kind' => 'none'];
    }

    private static function cut(string $text, int $limit): string
    {
        return Str::limit(trim($text), $limit, '');
    }
}
