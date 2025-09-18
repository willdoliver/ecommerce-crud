<?php

namespace App\Order\Services;

use App\Order\Models\Order;
use App\Order\Repositories\OrderRepository;

class OrderService implements OrderServiceInterface
{
    private OrderRepository $orderRepository;

    public function __construct()
    {
        $this->orderRepository = new OrderRepository();
    }

    public function findById(int $id): ?Order
    {
        return $this->orderRepository->findById($id);
    }

    public function listByUser(int $userId): array
    {
        return $this->orderRepository->findAllByUserId($userId);
    }

    public function create(int $userId, string $description, float $value, string $currency): Order
    {
        // Aqui poderiam entrar validações de negócio, como limites de valor, etc.
        return $this->orderRepository->create($userId, $description, $value, $currency);
    }

    public function update(int $id, array $data): ?Order
    {
        // Validações de negócio para a atualização poderiam ser adicionadas aqui.
        return $this->orderRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->orderRepository->delete($id);
    }
}