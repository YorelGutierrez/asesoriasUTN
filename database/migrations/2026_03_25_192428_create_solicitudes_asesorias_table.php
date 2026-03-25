<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_asesoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('materia_id')->nullable()->constrained();
            $table->string('materia_nombre')->nullable();
            $table->string('tema');
            $table->text('dudas');
            $table->text('motivo')->nullable();
            $table->enum('estado', ['pendiente', 'atendida', 'cancelada'])->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_asesoria');
    }
};
