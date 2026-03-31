<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archivos_asesoria', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_referencia');
            $table->enum('tipo_referencia', ['solicitud', 'sesion']);
            $table->string('nombre_archivo');
            $table->string('ruta');
            $table->foreignId('subido_por')->constrained('users')->onDelete('cascade');
            $table->index(['tipo_referencia', 'id_referencia']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archivos_asesoria');
    }
};
