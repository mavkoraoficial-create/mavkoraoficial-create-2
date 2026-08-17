<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['conversation_id', 'wa_message_id', 'direction', 'type', 'body', 'payload', 'generated_by'])]
class Message extends Model
{
    public const IN = 'in';
    public const OUT = 'out';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function isIncoming(): bool
    {
        return $this->direction === self::IN;
    }
}
