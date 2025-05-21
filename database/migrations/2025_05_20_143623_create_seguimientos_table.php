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
    Schema::create('seguimientos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('paciente_id')->constrained()->onDelete('cascade');
        $table->date('fecha_seguimiento');
        $table->float('pcr')->nullable();
        $table->float('vsg')->nullable();
	    $table->float('creatinina')->nullable();
	    $table->float('hb')->nullable();
	    $table->float('recuento_plaquetas')->nullable();
	    $table->float('neutrofilos')->nullable();
	    $table->float('ast')->nullable();
	    $table->float('alt')->nullable();
	    $table->float('proteinurea')->nullable();
	    $table->string('vih', 20)->nullable();
	    $table->date('fecha_carga_viral')->nullable();
	    $table->float('carga_viral')->nullable();
	    $table->date('fecha_cd4')->nullable();
	    $table->integer('cd4')->nullable();
	    $table->float('tuberculina')->nullable();
	    $table->float('trigliceridos')->nullable();
        $table->boolean('es_adherente')->default(false);
        $table->text('motivo_no_adherencia')->nullable();
        $table->boolean('hay_reaccion_adversa')->default(false);
        $table->string('naranjo_resultado')->nullable();
        $table->text('observaciones')->nullable();

        // Campos RAM adicionales
        $table->string('medicamento_nombre')->nullable();
        $table->string('marca_comercial')->nullable();
        $table->string('lote')->nullable();
        $table->date('fecha_vencimiento')->nullable();
        $table->string('registro_sanitario')->nullable();
        $table->text('descripcion_reaccion')->nullable();
        $table->enum('gravedad_reaccion', ['leve', 'moderada', 'grave'])->nullable();
        $table->text('acciones_tomadas')->nullable();

        $table->timestamps();
    });
}

public function down(): void
    {
        Schema::dropIfExists('seguimientos');
    }
};
