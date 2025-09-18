<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// 1. Cria o contêiner de injeção de dependência
$container = new \App\Shared\Core\Container();

// 2. Registra as "receitas" de como construir os objetos
// Quando alguém pedir a Interface, o contêiner saberá qual Classe concreta instanciar.
$container->bind(\App\User\Services\UserServiceInterface::class, \App\User\Services\UserService::class);
$container->bind(\App\User\Repositories\UserRepositoryInterface::class, \App\User\Repositories\UserRepository::class);
$container->bind(\App\Order\Services\OrderServiceInterface::class, \App\Order\Services\OrderService::class);
$container->bind(\App\Order\Repositories\OrderRepositoryInterface::class, \App\Order\Repositories\OrderRepository::class);

// 3. Passa o contêiner para o Router
\App\Shared\Core\Router::setContainer($container);

require_once __DIR__ . '/../routes/api.php';

\App\Shared\Core\Router::dispatch();