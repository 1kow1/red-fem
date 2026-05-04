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

        return response()->json($result, 200);
    }

    public function liberar(int $id, FormularioService $service)
    {
        $request->validate([
            'liberadoParaUso' => 'required|boolean'
        ]);

        $liberar = $request->boolean('liberadoParaUso');
        $form = $service->liberarParaUso($id, $liberar);

        return response()->json($form, 200);
    }

    public function buscar(Request $request, FormularioService $service)
    {
        $result = $service->filtrar($request);

        return response()->json($result);
    }
}
