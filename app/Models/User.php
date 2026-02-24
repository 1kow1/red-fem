<?php

namespace App\Models;

use App\Enums\Cargo;
use App\Enums\Especialidade;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = [
        'nome',
        'email',
        'password',
        'cargo',
        'telefone',
        'crm',
        'especialidade',
        'ativo',
        'temporary_password'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'cargo' => Cargo::class,
        'especialidade' => Especialidade::class,
        'ativo' => 'boolean',
        'temporary_password' => 'boolean',
    ];
}
