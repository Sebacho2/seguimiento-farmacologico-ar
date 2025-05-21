<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seguimientos', function (Blueprint $table) {
            $table->string('medicamento_nombre')->nullable();
            $table->string('marca_comercial')->nullable();
            $table->string('lote')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->string('registro_sanitario')->nullable();
            $table->text('descripcion_reaccion')->nullable();
            $table->enum('gravedad_reaccion', ['leve', 'moderada', 'grave'])->nullable();
            $table->text('acciones_tomadas')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('seguimientos', function (Blueprint $table) {
            $table->dropColumn([
                'medicamento_nombre',
                'marca_comercial',
                'lote',
                'fecha_vencimiento',
                'registro_sanitario',
                'descripcion_reaccion',
                'gravedad_reaccion',
                'acciones_tomadas',
            ]);
        });
    }
};