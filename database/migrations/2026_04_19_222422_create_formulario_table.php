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
 Schema::create('formularios', function (Blueprint $table) {
    $table->id();

    $table->foreignId('formulario_pai_id')->nullable()
        ->constrained('formularios')
        ->nullOnDelete();

    $table->integer('versao');

    $table->boolean('ativo')->default(true);
    $table->boolean('liberado_para_uso')->default(false);

    $table->string('titulo');
    $table->text('descricao')->nullable();

    $table->string('especialidade');

    $table->foreignId('medico_id')->constrained('users');

    $table->string('hash', 64);

    $table->timestamps();
});
    }

    
    public function down(): void
    {
        Schema::dropIfExists('formulario');
    }
};
