<?php


namespace App\Exceptions;

use Exception;

class AuthorizationException extends Exception
{
    protected $message = 'Não autorizado';
    protected $code = 403;
}