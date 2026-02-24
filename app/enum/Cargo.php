<?php

namespace App\Enums;

enum Cargo: string
{
    case ADMIN = 'ADMIN';
    case SECRETARIA = 'SECRETARIA';
    case MEDICO = 'MEDICO';
    case ACADEMICO = 'ACADEMICO';
}
