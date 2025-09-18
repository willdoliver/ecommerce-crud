<?php

namespace App\User\Repositories;

use App\User\Models\User;
use App\Shared\Core\Database;
use PDO;

class UserRepository implements UserRepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, User::class);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->db->prepare("SELECT id, name, email, created_at, updated_at FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, User::class);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    public function create(string $name, string $email, string $passwordHash): User
    {
        $sql = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password) RETURNING id, name, email, created_at, updated_at";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['name' => $name, 'email' => $email, 'password' => $passwordHash]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, User::class);
        return $stmt->fetch();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT id, name, email, created_at, updated_at FROM users ORDER BY id");
        return $stmt->fetchAll(PDO::FETCH_CLASS, User::class);
    }

    public function update(int $id, array $data): ?User
    {
        $fields = [];
        $params = ['id' => $id];

        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $params[$key] = $value;
        }

        $fields[] = "updated_at = CURRENT_TIMESTAMP";

        $sql = "UPDATE users SET " . implode(
            ', ',
            $fields
            ) . " WHERE id = :id RETURNING id, name, email, created_at, updated_at";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $stmt->setFetchMode(PDO::FETCH_CLASS, User::class);
        return $stmt->fetch() ?: null;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }
}