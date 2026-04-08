<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asesorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('docente_id')->constrained('users')->onDelete('cascade');
            $table->enum('tipo', ['individual', 'grupal']);
            $table->unsignedBigInteger('alumno_id')->nullable();
            $table->unsignedBigInteger('grupo_id')->nullable();
            $table->string('materia');
            $table->string('tema');
            $table->enum('modalidad', ['presencial', 'virtual']);
            $table->date('fecha');
            $table->time('hora');
            $table->text('preguntas')->nullable();
            $table->enum('estado', ['pendiente', 'completada', 'cancelada'])->default('pendiente');
            $table->timestamps();

            $table->foreign('alumno_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('grupo_id')->references('id')->on('grupos')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asesorias');
    }
};
