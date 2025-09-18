<?php

namespace App\Order\Services;

use App\Order\Models\Order;

interface OrderServiceInterface
{
    public function findById(int $id): ?Order;
    public function listByUser(int $userId): array;
    public function create(int $userId, string $description, float $value, string $currency): Order;
    public function update(int $id, array $data): ?Order;
    public function delete(int $id): bool;
}
