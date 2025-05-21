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
    Schema::table('pacientes', function (Blueprint $table) {
        $table->text('diagnostico')->nullable();
    });
}

public function down(): void
{
    Schema::table('pacientes', function (Blueprint $table) {
        $table->dropColumn('diagnostico');
    });
}
};
