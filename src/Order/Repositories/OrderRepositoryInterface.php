<?php

namespace App\Order\Repositories;

use App\Order\Models\Order;

interface OrderRepositoryInterface
{
    public function create(int $user_id, string $description, float $value, string $currency): Order;
    public function findById(int $id): ?Order;
    public function findAllByUserId(int $userId): array;
    public function update(int $id, array $data): ?Order;
    public function delete(int $id): bool;
}
