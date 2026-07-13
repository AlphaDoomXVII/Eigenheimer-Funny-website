<?php

require __DIR__ . '/../app/bootstrap.php';

use App\Core\Router;
use App\Modules\Bestellen\BestellenController;
use App\Modules\Kamers\KamerController;

$router = new Router();

$router->get('/', [BestellenController::class, 'index']);
$router->post('/bestellen/mand', [BestellenController::class, 'store']);
$router->post('/bestellen/mand/{uuid}/verwijderen', [BestellenController::class, 'destroy']);

$router->get('/kamers', [KamerController::class, 'index']);
$router->get('/kamers/beheer', [KamerController::class, 'beheer']);
$router->get('/kamers/nieuw', [KamerController::class, 'create']);
$router->post('/kamers', [KamerController::class, 'store']);
$router->get('/kamers/{id}/bewerken', [KamerController::class, 'edit']);
$router->post('/kamers/{id}', [KamerController::class, 'update']);
$router->post('/kamers/{id}/verwijderen', [KamerController::class, 'destroy']);
$router->post('/kamers/{id}/toggle', [KamerController::class, 'toggle']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
