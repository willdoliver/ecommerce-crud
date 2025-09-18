<?php

namespace App\Order\Repositories;

use App\Order\Models\Order;
use App\Shared\Core\Database;
use PDO;

class OrderRepository implements OrderRepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create(int $user_id, string $description, float $value, string $currency): Order
    {
        $sql = "INSERT INTO orders (user_id, description, value, currency) VALUES (:uid, :desc, :val, :currency) RETURNING *";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'uid' => $user_id,
            'desc' => $description,
            'val' => $value,
            'currency' => $currency
        ]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Order::class);
        return $stmt->fetch();
    }
    
    public function findById(int $id): ?Order
    {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Order::class);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findAllByUserId(int $userId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, Order::class);
    }

    public function update(int $id, array $data): ?Order
    {
        $fields = [];
        $params = ['id' => $id];

        // Lista de campos permitidos para atualização
        $allowedFields = ['description', 'value', 'currency'];

        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $fields[] = "$key = :$key";
                $params[$key] = $value;
            }
        }

        if (empty($fields)) {
            return $this->findById($id);
        }

        $sql = "UPDATE orders SET " . implode(', ', $fields) . ", updated_at = CURRENT_TIMESTAMP WHERE id = :id RETURNING *";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Order::class);
        return $stmt->fetch() ?: null;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM orders WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }
}