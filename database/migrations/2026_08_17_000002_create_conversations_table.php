<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();

            // Número del cliente en formato E.164 sin '+', tal como lo entrega Meta.
            $table->string('wa_id', 32)->unique();
            $table->string('profile_name')->nullable();

            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();

            // bot: el bot responde | human: un asesor tomó la conversación | closed: cerrada
            $table->string('status', 16)->default('bot');

            // Paso actual de la máquina de estados (ver App\Services\Bot\ConversationFlow).
            $table->string('step', 48)->default('start');

            // Datos parciales que el bot va recolectando antes de crear el lead.
            $table->json('context')->nullable();

            $table->timestamp('last_message_at')->nullable();
            $table->timestamp('handoff_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'last_message_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
