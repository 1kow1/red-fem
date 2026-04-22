<?php

namespace App\Exceptions;

use Exception;

class NotFoundException extends Exception
{
    protected $message = 'Recurso não encontrado';
    protected $code = 404;
}