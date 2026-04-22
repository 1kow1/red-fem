<?php
namespace App\Services;

use App\Exceptions\AuthorizationException;
use App\Models\User;

class AuthzService
{
    public function podeCriarFormulario(User $user, string $especialidade): void
    {
        if ($user->cargo === 'ADMIN') {
            return;
        }

        if ($user->especialidade !== $especialidade) {
            throw new AuthorizationException();
        }
    }

    public function podeAlterarConsulta(User $user): void
    {
        if (!in_array($user->cargo, ['ADMIN', 'SECRETARIA'])) {
            throw new AuthorizationException();
        }
    }

    public function podeVerFormulario(User $user): void
    {
        if (in_array($user->cargo, ['ADMIN', 'MEDICO', 'ACADEMICO'])) {
            return;
        }

        throw new AuthorizationException();
    }

    public function podeLiberarFormulario(User $user, Formulario $form): void
{
    if ($user->cargo !== 'MEDICO' || $user->cargo !== 'ADMIN') {
        throw new AuthorizationException();
    }

    if ($user->cargo !== 'ADMIN' && $user->especialidade !== $form->especialidade) {
        throw new AuthorizationException();
    }
}
}
