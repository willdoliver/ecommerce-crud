<?php

namespace App\User\Controllers;

use App\Shared\Core\Response;
use App\User\Services\UserServiceInterface;
use App\Shared\Services\AuthService;

class AuthController
{
    private UserServiceInterface $userService;
    
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function login(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $email = $data['email'] ?? null;
        $senha = $data['password'] ?? null;

        if (!$email || !$senha) {
            Response::json(['error' => 'Email e senha são obrigatórios'], 400);
            return;
        }

        $user = $this->userService->findByEmail($email);

        if (!$user || !password_verify($senha, $user->password)) {
            Response::json(['error' => 'Credenciais inválidas'], 401);
            return;
        }

        $token = AuthService::generateToken($user->id, $user->email, $user->name);
        Response::json(['token' => $token]);
    }
}