<?php

namespace App\Shared\Services;

use App\Shared\Core\Response;

class AuthorizationService
{
    private ?object $authUser;

    public function __construct(?object $authUser)
    {
        $this->authUser = $authUser;
    }

    public function requireAuth(): void
    {
        if (!$this->authUser) {
            Response::json(['error' => 'Acesso não autorizado. Requer autenticação.'], 401);
        }
    }

    public function getAuthUserId(): int
    {
        $this->requireAuth();
        return (int) $this->authUser->sub;
    }

    public function authorizeOwner(int $userId): void
    {
        $this->requireAuth();

        if ($this->getAuthUserId() !== $userId) {
            Response::json(['error' => 'Acesso negado. Você não tem permissão para acessar este recurso.'], 403);
        }
    }
}