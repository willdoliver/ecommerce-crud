<?php
use App\Shared\Core\Router;
use App\Shared\Middleware\AuthMiddleware;

Router::add('POST', '/auth/login', [\App\User\Controllers\AuthController::class, 'login']);

Router::add('POST', '/users', [\App\User\Controllers\UserController::class, 'create']);
Router::add('GET', '/users/{id}', [\App\User\Controllers\UserController::class, 'find'], [AuthMiddleware::class]);
Router::add('GET', '/users', [\App\User\Controllers\UserController::class, 'list'], [AuthMiddleware::class]);
Router::add('PUT', '/users', [\App\User\Controllers\UserController::class, 'update'], [AuthMiddleware::class]);
Router::add('DELETE', '/users', [\App\User\Controllers\UserController::class, 'delete'], [AuthMiddleware::class]);

Router::add('POST', '/orders', [\App\Order\Controllers\OrderController::class, 'create'], [AuthMiddleware::class]);
Router::add('GET', '/orders/{id}', [\App\Order\Controllers\OrderController::class, 'find'], [AuthMiddleware::class]);
Router::add('GET', '/orders', [\App\Order\Controllers\OrderController::class, 'list'], [AuthMiddleware::class]);
Router::add('PUT', '/orders/{id}', [\App\Order\Controllers\OrderController::class, 'update'], [AuthMiddleware::class]);
Router::add('DELETE', '/orders/{id}', [\App\Order\Controllers\OrderController::class, 'delete'], [AuthMiddleware::class]);
