<?php

namespace App\Shared\Middleware;

use App\Shared\Core\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware
{
    public static function handle(): object
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;

        if (!$authHeader) {
            Response::json(['error' => 'Token de autorização não fornecido'], 401);
        }

        $token = str_replace('Bearer ', '', $authHeader);

        try {
            $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
            return $decoded; // Retorna o payload decodificado do token
        } catch (\Exception $e) {
            Response::json(['error' => 'Token inválido ou expirado'], 401);
        }
    }
}