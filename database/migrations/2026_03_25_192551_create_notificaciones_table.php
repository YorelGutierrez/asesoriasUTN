<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->enum('tipo', ['solicitud_asesoria', 'confirmacion', 'rechazo', 'recordatorio']);
            $table->text('mensaje');
            $table->boolean('leido')->default(false);
            $table->json('datos')->nullable();
            $table->enum('accion', ['pendiente', 'confirmada', 'rechazada'])->default('pendiente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
