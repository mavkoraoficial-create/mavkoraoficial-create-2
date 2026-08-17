<?php

namespace App\Services\Bot;

use App\Models\Appointment;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;

/**
 * Calcula las franjas libres para la reunión inicial.
 *
 * Cuidado con las zonas horarias: la aplicación corre en UTC (config/app.php)
 * pero el horario de atención está definido en hora de Colombia. Todo el
 * cálculo se hace en la zona del negocio y solo se pasa a UTC al tocar la base
 * de datos. Mezclar las dos es la forma más fácil de ofrecer una reunión a las
 * 4 de la mañana.
 */
class SlotFinder
{
    private string $timezone;

    /** @var list<int> */
    private array $workingDays;

    private int $slotMinutes;

    public function __construct()
    {
        $this->timezone = config('mavkora.schedule.timezone', 'America/Bogota');
        $this->workingDays = config('mavkora.schedule.days', [1, 2, 3, 4, 5]);
        $this->slotMinutes = (int) config('mavkora.schedule.slot_minutes', 30);
    }

    /**
     * Franjas libres, ya ordenadas y listas para pintar en una lista de WhatsApp.
     *
     * @return list<array{id: string, at: CarbonImmutable, title: string, description: string}>
     */
    public function available(?int $limit = null): array
    {
        $limit ??= (int) config('mavkora.schedule.slots_offered', 8);
        $horizonDays = (int) config('mavkora.schedule.horizon_days', 10);
        $leadHours = (int) config('mavkora.schedule.lead_time_hours', 24);

        $earliest = CarbonImmutable::now($this->timezone)->addHours($leadHours);
        $taken = $this->takenSlots($earliest, $earliest->addDays($horizonDays));

        $slots = [];
        $day = $earliest->startOfDay();

        for ($i = 0; $i <= $horizonDays && count($slots) < $limit; $i++, $day = $day->addDay()) {
            if (! in_array($day->dayOfWeekIso, $this->workingDays, true)) {
                continue;
            }

            foreach ($this->slotsForDay($day) as $slot) {
                if (count($slots) >= $limit) {
                    break;
                }

                if ($slot->lessThan($earliest) || isset($taken[$this->key($slot)])) {
                    continue;
                }

                $slots[] = [
                    'id' => self::idFor($slot),
                    'at' => $slot,
                    'title' => self::label($slot),
                    'description' => sprintf('Reunión inicial · %d min', $this->slotMinutes),
                ];
            }
        }

        return $slots;
    }

    /**
     * Convierte el id de una fila de WhatsApp de vuelta a una fecha.
     *
     * Devuelve null si el id viene manipulado, si la franja quedó fuera del
     * horario o si alguien más la tomó mientras el cliente decidía.
     */
    public function resolve(string $slotId): ?CarbonImmutable
    {
        if (! preg_match('/^slot_(\d{4}-\d{2}-\d{2})_(\d{2})(\d{2})$/', $slotId, $m)) {
            return null;
        }

        try {
            $at = CarbonImmutable::createFromFormat(
                'Y-m-d H:i',
                "{$m[1]} {$m[2]}:{$m[3]}",
                $this->timezone
            );
        } catch (\Throwable) {
            return null;
        }

        if ($at === false || ! $this->isWithinSchedule($at) || ! $this->isFree($at)) {
            return null;
        }

        $leadHours = (int) config('mavkora.schedule.lead_time_hours', 24);

        if ($at->lessThan(CarbonImmutable::now($this->timezone)->addHours($leadHours))) {
            return null;
        }

        return $at;
    }

    public function isFree(CarbonImmutable $at): bool
    {
        return ! Appointment::query()
            ->whereIn('status', Appointment::BLOCKING_STATUSES)
            ->where('scheduled_at', $at->utc()->toDateTimeString())
            ->exists();
    }

    public static function idFor(CarbonImmutable $at): string
    {
        return 'slot_'.$at->format('Y-m-d_Hi');
    }

    /**
     * Etiqueta corta en español. Meta recorta los títulos de fila a 24
     * caracteres, así que el formato es deliberadamente compacto.
     */
    public static function label(CarbonImmutable $at): string
    {
        return Str::ucfirst($at->locale('es')->isoFormat('ddd D MMM')).' · '.ltrim($at->format('g:i a'), '0');
    }

    /**
     * Fecha larga para las confirmaciones, donde sí hay espacio.
     */
    public static function longLabel(CarbonImmutable $at): string
    {
        return Str::ucfirst($at->locale('es')->isoFormat('dddd D [de] MMMM')).' a las '.ltrim($at->format('g:i a'), '0');
    }

    /**
     * @return list<CarbonImmutable>
     */
    private function slotsForDay(CarbonImmutable $day): array
    {
        [$startHour, $startMinute] = $this->parseTime(config('mavkora.schedule.start', '09:00'));
        [$endHour, $endMinute] = $this->parseTime(config('mavkora.schedule.end', '17:00'));

        $cursor = $day->setTime($startHour, $startMinute);
        $end = $day->setTime($endHour, $endMinute);

        $slots = [];

        // La comparación es estricta para que una franja no arranque justo a la
        // hora de cierre: la última posible empieza slotMinutes antes.
        while ($cursor->addMinutes($this->slotMinutes)->lessThanOrEqualTo($end)) {
            $slots[] = $cursor;
            $cursor = $cursor->addMinutes($this->slotMinutes);
        }

        return $slots;
    }

    private function isWithinSchedule(CarbonImmutable $at): bool
    {
        if (! in_array($at->dayOfWeekIso, $this->workingDays, true)) {
            return false;
        }

        foreach ($this->slotsForDay($at->startOfDay()) as $slot) {
            if ($slot->equalTo($at)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Citas ya ocupadas en la ventana, indexadas para consultar en memoria.
     *
     * @return array<string, true>
     */
    private function takenSlots(CarbonImmutable $from, CarbonImmutable $to): array
    {
        return Appointment::query()
            ->whereIn('status', Appointment::BLOCKING_STATUSES)
            ->whereBetween('scheduled_at', [$from->utc(), $to->utc()])
            ->pluck('scheduled_at')
            ->mapWithKeys(fn ($at) => [
                $this->key(CarbonImmutable::parse($at)->setTimezone($this->timezone)) => true,
            ])
            ->all();
    }

    private function key(CarbonImmutable $at): string
    {
        return $at->format('Y-m-d H:i');
    }

    /**
     * @return array{int, int}
     */
    private function parseTime(string $time): array
    {
        [$hour, $minute] = array_pad(explode(':', $time), 2, '0');

        return [(int) $hour, (int) $minute];
    }
}
