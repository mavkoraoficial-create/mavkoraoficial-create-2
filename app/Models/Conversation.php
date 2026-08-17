<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['wa_id', 'profile_name', 'lead_id', 'status', 'step', 'context', 'last_message_at', 'handoff_at'])]
class Conversation extends Model
{
    public const STATUS_BOT = 'bot';
    public const STATUS_HUMAN = 'human';
    public const STATUS_CLOSED = 'closed';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'context' => 'array',
            'last_message_at' => 'datetime',
            'handoff_at' => 'datetime',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Lee un dato del contexto parcial que el bot va recolectando.
     */
    public function contextGet(string $key, mixed $default = null): mixed
    {
        return data_get($this->context ?? [], $key, $default);
    }

    /**
     * Escribe en el contexto sin pisar el resto de las claves.
     */
    public function contextPut(string $key, mixed $value): void
    {
        $context = $this->context ?? [];
        data_set($context, $key, $value);
        $this->context = $context;
    }

    public function contextForget(string ...$keys): void
    {
        $context = $this->context ?? [];

        foreach ($keys as $key) {
            unset($context[$key]);
        }

        $this->context = $context;
    }

    /**
     * ¿Pasó tanto tiempo desde el último mensaje que conviene reiniciar?
     *
     * Sin esto, alguien que abandonó a mitad del formulario de cotización y
     * vuelve tres días después recibiría "¿cuál es tu correo?" sin contexto.
     */
    public function isStale(): bool
    {
        if ($this->last_message_at === null) {
            return false;
        }

        $hours = (int) config('mavkora.bot.session_timeout_hours', 12);

        return $this->last_message_at->addHours($hours)->isPast();
    }

    /**
     * Tras un escalado, el bot calla un rato para no pisar al asesor.
     */
    public function isSilencedForHandoff(): bool
    {
        if ($this->status !== self::STATUS_HUMAN || $this->handoff_at === null) {
            return false;
        }

        $hours = (int) config('mavkora.bot.handoff_silence_hours', 24);

        return $this->handoff_at->addHours($hours)->isFuture();
    }
}
