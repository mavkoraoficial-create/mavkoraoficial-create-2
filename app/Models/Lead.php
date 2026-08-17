<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['name', 'email', 'phone', 'company', 'service', 'message', 'source', 'status', 'notes'])]
class Lead extends Model
{
    public const STATUSES = [
        'new' => 'Nuevo',
        'contacted' => 'Contactado',
        'qualified' => 'Calificado',
        'won' => 'Ganado',
        'lost' => 'Perdido',
    ];

    public const SOURCES = [
        'web' => 'Formulario web',
        'whatsapp' => 'WhatsApp',
    ];

    public function conversation(): HasOne
    {
        return $this->hasOne(Conversation::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Nombre legible del servicio. Cae al código crudo si el catálogo cambió
     * y quedaron leads antiguos apuntando a un servicio que ya no existe.
     */
    public function serviceName(): string
    {
        if (blank($this->service)) {
            return 'Sin especificar';
        }

        return config("mavkora.services.{$this->service}.name", $this->service);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function sourceLabel(): string
    {
        return self::SOURCES[$this->source] ?? $this->source;
    }
}
