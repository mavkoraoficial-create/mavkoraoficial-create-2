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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 32)->nullable();
            $table->string('company')->nullable();

            // Coincide con los value de #c_services en el modal de cotización:
            // web, mobile, ai, cloud, cyber, support
            $table->string('service', 32)->nullable();
            $table->text('message')->nullable();

            // De dónde llegó: web (formulario) o whatsapp (bot)
            $table->string('source', 32)->default('web');
            $table->string('status', 32)->default('new');

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('phone');
            $table->index(['status', 'created_at']);
            $table->index('source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
