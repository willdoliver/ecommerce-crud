<?php
namespace App\Order\Controllers;

use App\Shared\Core\Response;
use App\Shared\Services\AuthorizationService;
use App\Shared\Services\CurrencyService;
use App\Order\Services\OrderServiceInterface;

class OrderController
{
    private OrderServiceInterface $orderService;
    private CurrencyService $currencyService;
    private AuthorizationService $auth;
    
    public function __construct(OrderServiceInterface $orderService, CurrencyService $currencyService, ?object $authUser = null)
    {
        $this->auth = new AuthorizationService($authUser);
        $this->auth->requireAuth(); // Garante que todas as rotas deste controller sejam para usuários autenticados.
        $this->orderService = $orderService;
        $this->currencyService = $currencyService;
    }

    public function create(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        // TODO: Adicionar validação para os dados de entrada
        $userId = $this->auth->getAuthUserId();

        $order = $this->orderService->create(
            $userId,
            $data['description'],
            $data['value'],
            $data['currency']
        );

        $convertedValues = $this->currencyService->getConvertedValues($order->value, $order->currency);
        $response = array_merge((array)$order, $convertedValues);

        Response::json($response, 201);
    }

    public function find(int $id): void
    {
        $order = $this->orderService->findById($id);

        if (!$order) {
            Response::json(['error' => 'Pedido não encontrado'], 404);
            return;
        }

        // Garante que o usuário logado só possa ver seus próprios pedidos.
        $this->auth->authorizeOwner($order->user_id);

        $convertedValues = $this->currencyService->getConvertedValues($order->value, $order->currency);
        $response = array_merge((array)$order, $convertedValues);

        Response::json($response);
    }

    public function list(): void
    {
        $userId = $this->auth->getAuthUserId();
        $orders = $this->orderService->listByUser($userId);

        // Opcional: Converte a moeda para todos os pedidos da lista
        $response = array_map(function ($order) {
            $convertedValues = $this->currencyService->getConvertedValues((float)$order->value, $order->currency);
            return array_merge((array)$order, $convertedValues);
        }, $orders);

        Response::json($response);
    }

    public function update(int $id): void
    {
        $order = $this->orderService->findById($id);
        if (!$order) {
            Response::json(['error' => 'Pedido não encontrado'], 404);
            return;
        }

        // Garante que o usuário logado só possa editar seus próprios pedidos.
        $this->auth->authorizeOwner((int)$order->user_id);

        $data = json_decode(file_get_contents('php://input'), true);

        // Para PUT, todos os campos são esperados.
        if (empty($data['description']) || !isset($data['value']) || empty($data['currency'])) {
            Response::json(['error' => 'Para atualizar, todos os campos são obrigatórios: description, value, currency.'], 400);
            return;
        }

        $updatedOrder = $this->orderService->update($id, $data);

        $convertedValues = $this->currencyService->getConvertedValues((float)$updatedOrder->value, $updatedOrder->currency);
        $response = array_merge((array)$updatedOrder, $convertedValues);

        Response::json($response);
    }

    public function delete(int $id): void
    {
        $order = $this->orderService->findById($id);
        if (!$order) {
            Response::json(['error' => 'Pedido não encontrado'], 404);
            return;
        }

        $this->auth->authorizeOwner((int)$order->user_id);

        if (!$this->orderService->delete($id)) {
             // Isso não deve acontecer se a verificação acima passar, mas é uma boa salvaguarda.
            Response::json(['error' => 'Falha ao excluir o pedido'], 500);
            return;
        }

        Response::json(['message' => 'Pedido excluído com sucesso']);
    }
}