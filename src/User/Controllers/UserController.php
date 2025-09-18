<?php
namespace App\User\Controllers;

use App\Shared\Core\Response;
use App\Shared\Services\AuthorizationService;
use App\User\Services\UserServiceInterface;

class UserController
{
    private UserServiceInterface $userService;
    private AuthorizationService $auth;
    
    public function __construct(UserServiceInterface $userService, ?object $authUser = null)
    {
        $this->userService = $userService;
        $this->auth = new AuthorizationService($authUser);
    }

    public function create(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        // Validação básica
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            Response::json(['error' => 'Nome, email e senha são obrigatórios'], 400);
            return;
        }

        if ($this->userService->findByEmail($data['email'])) {
            Response::json(['error' => 'Email já cadastrado'], 409);
            return;
        }

        $senhaHash = password_hash($data['password'], PASSWORD_DEFAULT);
        $user = $this->userService->create($data['name'], $data['email'], $senhaHash);
        
        Response::json($user, 201);
    }
    
    public function find(int $id): void
    {
        $this->auth->authorizeOwner($id);

        $user = $this->userService->findById($id);
        if (!$user) {
            Response::json(['error' => 'Usuário não encontrado'], 404);
            return;
        }
        Response::json($user);
    }

    public function list(): void
    {
        $this->auth->requireAuth();
        $users = $this->userService->list();
        Response::json($users);
    }

    public function update(): void
    {
        $userId = $this->auth->getAuthUserId();
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            Response::json(['error' => 'Para atualizar, todos os campos são obrigatórios: name, email e password.'], 400);
            return;
        }

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ];

        $updatedUser = $this->userService->update($userId, $updateData);

        if ($updatedUser === null) {
            Response::json(['error' => 'Email já está em uso por outro usuário'], 409);
            return;
        }

        Response::json($updatedUser);
    }

    public function delete(): void
    {
        $userId = $this->auth->getAuthUserId();
        $success = $this->userService->delete($userId);

        if (!$success) {
            Response::json(['error' => 'Usuário não encontrado para exclusão'], 404);
            return;
        }
        Response::json(['message' => 'Usuário excluído com sucesso'], 200);
    }
}