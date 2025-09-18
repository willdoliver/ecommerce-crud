<?php

namespace App\User\Services;

use App\User\Models\User;

interface UserServiceInterface
{
    public function findByEmail(string $email): ?User;
    public function create(string $nome, string $email, string $senhaHash): User;
    public function findById(int $id): ?User;
    public function list(): array;
    public function update(int $id, array $data): ?User;
    public function delete(int $id): bool;
}
