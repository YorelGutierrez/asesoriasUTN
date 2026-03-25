<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acuerdos_asesoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesion_id')->constrained('sesiones_asesoria')->onDelete('cascade');
            $table->foreignId('alumno_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->text('acuerdo');
            $table->text('compromiso_alumno')->nullable();
            $table->text('compromiso_docente')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acuerdos_asesoria');
    }
};
