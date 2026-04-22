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
    Schema::create('formulario_pergunta', function (Blueprint $table) {

    $table->foreignId('formulario_id')
          ->constrained('formularios')
          ->cascadeOnDelete();

    $table->foreignId('pergunta_id')
          ->constrained('perguntas')
          ->cascadeOnDelete();

    $table->integer('posicao');

    $table->primary(['formulario_id', 'pergunta_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formulario_pergunta');
    }
};
