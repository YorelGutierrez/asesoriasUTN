<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('docentes', function (Blueprint $table) {
            $table->foreignId('carrera_id')->nullable()->constrained('carreras')->onDelete('set null');
            $table->dropColumn('departamento'); // Eliminar campo antiguo
        });
    }

    public function down(): void
    {
        Schema::table('docentes', function (Blueprint $table) {
            $table->dropForeign(['carrera_id']);
            $table->dropColumn('carrera_id');
            $table->string('departamento')->nullable();
        });
    }
};