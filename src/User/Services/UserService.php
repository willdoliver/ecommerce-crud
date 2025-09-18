<?php
namespace App\User\Services;

use App\User\Repositories\UserRepositoryInterface;
use App\User\Models\User;

class UserService implements UserServiceInterface
{
    private UserRepositoryInterface $userRepository;
    
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function findByEmail(string $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }

    public function create(string $nome, string $email, string $senhaHash): User
    {
        return $this->userRepository->create($nome, $email, $senhaHash);
    }

    public function findById(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    public function list(): array
    {
        return $this->userRepository->findAll();
    }

    public function update(int $id, array $data): ?User
    {
        $existingUser = $this->userRepository->findByEmail($data['email']);
        if ($existingUser && $existingUser->id !== $id) {
            return null;
        }

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        return $this->userRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->userRepository->delete($id);
    }
}