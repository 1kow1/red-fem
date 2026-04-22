<?php

namespace App\Http\Controllers;

use App\Services\FormularioService;
use Illuminate\Http\Request;

class FormularioController extends Controller
{
    public function store(Request $request, FormularioService $service)
    {
        $formulario = $service->salvar($request->all());

        return response()->json($formulario, 201);
    }

    public function show($id, FormularioService $service)
    {
        $formulario = $service->buscarPorId($id);

        return response()->json($formulario, 200);
    }

    public function index(Request $request, FormularioService $service)
    {
        $result = $service->listar($request);

        return response()->json($result);
    }

    public function liberar(int $id, FormularioService $service)
    {
        $request->validate([
            'liberado_para_uso' => 'required|boolean'
        ]);
        $form = $service->liberarParaUso($id, $request->boolean('liberadoParaUso'));

        return response()->json($form, 200);
    }
}
