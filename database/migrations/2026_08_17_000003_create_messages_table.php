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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();

            // ID que asigna Meta (wamid.xxx). Único para descartar webhooks repetidos:
            // Meta reintenta la entrega y sin esto el bot respondería dos veces.
            $table->string('wa_message_id', 191)->nullable()->unique();

            $table->string('direction', 8);          // in | out
            $table->string('type', 32)->default('text');
            $table->text('body')->nullable();
            $table->json('payload')->nullable();

            // Cómo se generó la respuesta: flow (menú), ai (Claude), human (asesor)
            $table->string('generated_by', 16)->nullable();

            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
