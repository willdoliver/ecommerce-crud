<?php

namespace App\User\Repositories;

use App\User\Models\User;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?User;
    public function findById(int $id): ?User;
    public function create(string $name, string $email, string $passwordHash): User;
    public function findAll(): array;
    public function update(int $id, array $data): ?User;
    public function delete(int $id): bool;
}
