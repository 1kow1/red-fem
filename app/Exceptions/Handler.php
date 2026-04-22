<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e)
    {
        if ($e instanceof AuthorizationException) {
            return response()->json([
                'error' => $e->getMessage()
            ], 403);
        }

        if ($e instanceof NotFoundException) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404);
        }

        return parent::render($request, $e);
    }
}