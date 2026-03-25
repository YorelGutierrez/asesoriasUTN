<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_academico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos')->onDelete('cascade');
            $table->foreignId('materia_id')->constrained()->onDelete('cascade');
            $table->integer('cuatrimestre');
            $table->boolean('reprobada')->default(false);
            $table->boolean('extraordinario')->default(false);
            $table->text('temas_no_dominados')->nullable();
            $table->timestamps();

            $table->unique(['alumno_id', 'materia_id', 'cuatrimestre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_academico');
    }
};
