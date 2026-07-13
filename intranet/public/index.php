<?php

define('APP_CONTEXT', 'intranet');

require __DIR__ . '/../../app/bootstrap.php';

use App\Core\Router;
use App\Modules\Bestellen\BestellenController;
use App\Modules\Dashboard\DashboardController;
use App\Modules\Kamers\KamerController;
use App\Shared\Auth\AuthController;
use App\Shared\Rechten\RechtenController;

$router = new Router();

$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);

$router->get('/', [DashboardController::class, 'index']);
$router->post('/bestellingen/{id}/afhandelen', [DashboardController::class, 'afhandelen']);

$router->get('/rechten', [RechtenController::class, 'index']);
$router->post('/rechten/{feature}/toggle', [RechtenController::class, 'toggle']);

$router->get('/kamers/beheer', [KamerController::class, 'beheer']);
$router->get('/kamers/nieuw', [KamerController::class, 'create']);
$router->post('/kamers', [KamerController::class, 'store']);
$router->get('/kamers/{id}/bewerken', [KamerController::class, 'edit']);
$router->post('/kamers/{id}', [KamerController::class, 'update']);
$router->post('/kamers/{id}/verwijderen', [KamerController::class, 'destroy']);
$router->post('/kamers/{id}/toggle', [KamerController::class, 'toggle']);

$router->get('/bestellen/beheer', [BestellenController::class, 'menuBeheer']);
$router->get('/bestellen/nieuw', [BestellenController::class, 'menuCreate']);
$router->post('/bestellen', [BestellenController::class, 'menuStore']);
$router->get('/bestellen/{id}/bewerken', [BestellenController::class, 'menuEdit']);
$router->post('/bestellen/{id}', [BestellenController::class, 'menuUpdate']);
$router->post('/bestellen/{id}/verwijderen', [BestellenController::class, 'menuDestroy']);
$router->post('/bestellen/{id}/toggle', [BestellenController::class, 'menuToggle']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
