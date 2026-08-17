<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['lead_id', 'conversation_id', 'scheduled_at', 'duration_minutes', 'status', 'notes'])]
class Appointment extends Model
{
    public const STATUSES = [
        'pending' => 'Por confirmar',
        'confirmed' => 'Confirmada',
        'cancelled' => 'Cancelada',
        'done' => 'Realizada',
    ];

    /**
     * Estados que ocupan una franja horaria. Una cita cancelada libera el cupo.
     */
    public const BLOCKING_STATUSES = ['pending', 'confirmed'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
