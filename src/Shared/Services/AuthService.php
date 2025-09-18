<?php

namespace App\Shared\Services;

use Firebase\JWT\JWT;

class AuthService
{
    public static function generateToken(int $userId, string $email, string $nome): string
    {
        $payload = [
            'iss' => $_ENV['APP_URL'] ?? 'http://localhost', // Emissor
            'iat' => time(), // Emitido em
            'exp' => time() + (int)$_ENV['JWT_EXP'], // Expiração
            'sub' => $userId, // Subject (ID do usuário)
            'data' => [ // Dados adicionais que você queira
                'email' => $email,
                'nome' => $nome
            ]
        ];

        return JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
    }
}