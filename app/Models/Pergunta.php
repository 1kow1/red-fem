<?php

class Pergunta extends Model
{
    protected $fillable = ['enunciado', 'tipo', 'ativo'];

    public function alternativas()
    {
        return $this->hasMany(Alternativa::class)
            ->orderBy('posicao');
    }

    public function formularios()
    {
        return $this->belongsToMany(Formulario::class, 'formulario_pergunta')
            ->withPivot('posicao');
    }
}