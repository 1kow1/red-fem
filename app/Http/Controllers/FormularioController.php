<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FormularioController extends Controller
{
    public function index() {}

    public function store(Request $request)
    {
        return response()->json([
            'message' => 'Success',
            'data' => $request,
            'usuario' => $request->user()
        ]);
    }
}
