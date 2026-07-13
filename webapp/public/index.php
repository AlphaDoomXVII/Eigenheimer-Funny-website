<?php

define('APP_CONTEXT', 'webapp');

require __DIR__ . '/../../app/bootstrap.php';

use App\Core\Router;
use App\Modules\Bestellen\BestellenController;
use App\Modules\Kamers\KamerController;

$router = new Router();

$router->get('/', [BestellenController::class, 'index']);
$router->post('/bestellen/mand', [BestellenController::class, 'store']);
$router->post('/bestellen/mand/{uuid}/verwijderen', [BestellenController::class, 'destroy']);
$router->post('/bestellen/afronden', [BestellenController::class, 'checkout']);

$router->get('/kamers', [KamerController::class, 'index']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
