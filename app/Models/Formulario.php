<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formulario extends Model
{
    protected $fillable = [
        'formulario_pai_id',
        'versao',
        'ativo',
        'liberado_para_uso',
        'titulo',
        'descricao',
        'especialidade',
        'medico_id',
        'hash'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'liberado_para_uso' => 'boolean',
    ];

    public function perguntas()
    {
        return $this->belongsToMany(Pergunta::class, 'formulario_pergunta')
            ->withPivot('posicao')
            ->orderBy('pivot_posicao');
    }

    public function medico()
    {
        return $this->belongsTo(User::class);
    }

    public function formularioPai()
    {
        return $this->belongsTo(Formulario::class, 'formulario_pai_id');
    }
}