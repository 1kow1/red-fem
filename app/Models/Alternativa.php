<?php
class Alternativa extends Model
{
    protected $fillable = ['pergunta_id', 'texto', 'posicao'];

    public function pergunta()
    {
        return $this->belongsTo(Pergunta::class);
    }
}