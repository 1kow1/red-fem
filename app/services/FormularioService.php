<?php
namespace App\Services;

use App\Models\Formulario;
use App\Models\Pergunta;
use Illuminate\Support\Facades\DB;

class FormularioService
{
    public function __construct(
        private AuthzService $authz
    ) {}

    public function gerarHashPayload(array $payload): string
    {
        $data = [
            'titulo' => $payload['titulo'],
            'descricao' => $payload['descricao'] ?? null,
            'especialidade' => $payload['especialidade'],
            'perguntas' => collect($payload['perguntas'])
                ->sortBy('posicao')
                ->map(function ($p) {
                    return [
                        'enunciado' => $p['enunciado'],
                        'tipo' => $p['tipo'],
                        'posicao' => $p['posicao'],
                        'alternativas' => collect($p['alternativas'] ?? [])
                            ->sortBy('posicao')
                            ->map(fn($a) => [
                                'texto' => $a['texto'],
                                'posicao' => $a['posicao']
                            ])
                            ->values()
                    ];
                })
                ->values()
        ];

        return hash('sha256', json_encode($data));
    }

    public function buscarPorId(int $id)
    {
        $user = auth()->user();

        $this->authz->podeVerFormulario($user);

        $form = Formulario::with('perguntas.alternativas')
            ->find($id);

        if (!$form) {
            throw new NotFoundException();
        }

        return $form;
    }

    public function listar(Request $request)
    {
        $user = auth()->user();

        $this->authz->podeVerFormulario($user);

        $query = Formulario::query();

        if ($user->cargo !== 'ADMIN') {
            $query->where('ativo', true);
        }

        if ($request->filled('especialidade')) {
            $query->where('especialidade', $request->get('especialidade'));
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->boolean('ativo'));
        }

        $query->select([
            'id',
            'titulo',
            'especialidade',
            'versao',
            'ativo',
            'liberado_para_uso',
            'created_at'
        ]);

        $query->orderByDesc('created_at');

        $perPage = max(1, min((int) $request->get('per_page', 10), 100));

        return $query->paginate($perPage);
    }

    public function liberarParaUso(int $id, bool $liberar)
    {
        return DB::transaction(function () use ($id, $liberar) {
            $user = auth()->user();
            $this->authz->podeLiberarFormulario($user, $form);
            $form = Formulario::find($id);

            if (!$form) {
                throw new NotFoundException();
            }

            if ($user->cargo !== 'ADMIN') {
                if ($user->especialidade !== $form->especialidade) {
                    throw new AuthorizationException();
                }
            }

            if ($form->liberado_para_uso && !$liberar) {
                throw new \Exception('Formulário já liberado não pode ser bloqueado');
            }

            if ($liberar && !$form->ativo) {
                throw new \Exception('Formulário inativo não pode ser liberado');
            }

            if ($liberar) {
                Formulario::where('especialidade', $form->especialidade)
                    ->where('liberado_para_uso', true)
                    ->where('id', '!=', $form->id)
                    ->update(['liberado_para_uso' => false]);
            }

            $form
                ->where()
                ->update([
                    'liberado_para_uso' => $liberar
                ]);

            return $form;
        });
    }

    public function salvar(array $payload): Formulario
    {
        return DB::transaction(function () use ($payload) {
            $user = auth()->user();
            $this->authz->podeCriarFormulario(
                $user,
                $payload['especialidade']
            );

            $hashNovo = $this->gerarHashPayload($payload);

            $formAtual = Formulario::where('ativo', true)
                ->where('especialidade', $payload['especialidade'])
                ->first();

            if ($formAtual && $formAtual->hash === $hashNovo) {
                return $formAtual;
            }

            if ($formAtual) {
                $formAtual->update(['ativo' => false]);
                $formAtual->update(['liberado_para_uso' => false]);
            }

            $novo = Formulario::create([
                'formulario_pai_id' => $formAtual?->id,
                'versao' => ($formAtual->versao ?? 0) + 1,
                'titulo' => $payload['titulo'],
                'descricao' => $payload['descricao'] ?? null,
                'especialidade' => $payload['especialidade'],
                'medico_id' => auth()->id(),
                'ativo' => true,
                'hash' => $hashNovo
            ]);

            foreach ($payload['perguntas'] as $p) {
                $pergunta = Pergunta::create([
                    'enunciado' => $p['enunciado'],
                    'tipo' => $p['tipo']
                ]);

                if (!empty($p['alternativas'])) {
                    $pergunta->alternativas()->createMany($p['alternativas']);
                }

                $novo->perguntas()->attach($pergunta->id, [
                    'posicao' => $p['posicao']
                ]);
            }

            return $novo;
        });
    }

    public function filtrar(Request $request)
    {
        $query = Formulario::query();

        if ($request->filled('ativo')) {
            $query->whereIn('ativo', (array) $request->get('ativo'));
        }

        if ($request->filled('liberado_para_uso')) {
            $query->whereIn('liberado_para_uso', (array) $request->get('liberado_para_uso'));
        }

        if ($request->filled('titulo')) {
            $query->whereIn('titulo', (array) $request->get('titulo'));
        }

        if ($request->filled('descricao')) {
            $query->whereIn('descricao', (array) $request->get('descricao'));
        }

        if ($request->filled('especialidade')) {
            $query->whereIn('especialidade', (array) $request->get('especialidade'));
        }

        if ($request->filled('versao')) {
            $query->whereIn('versao', (array) $request->get('versao'));
        }

        if ($request->filled('busca')) {
            $busca = $request->get('busca');

            $query->where(function ($q) use ($busca) {
                $q
                    ->where('titulo', 'like', "%{$busca}%")
                    ->orWhere('descricao', 'like', "%{$busca}%");
            });
        }

        if ($request->filled('medico_ids')) {
            $query->whereIn('medico_id', (array) $request->get('medico_ids'));
        }

        $sort = $request->get('sort', 'versao');
        $direction = $request->get('direction', 'desc');

        $query->orderBy($sort, $direction);

        $perPage = max(1, min((int) $request->get('per_page', 10), 100));

        return $query->paginate($perPage);
    }
}
