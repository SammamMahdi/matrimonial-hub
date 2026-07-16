<?php

declare(strict_types=1);

/**
 * Front controller — every request enters here.
 *
 * The original exposed one PHP file per page at the web root, which meant the
 * database credentials, an unzipper script and the raw SQL dump were all
 * directly fetchable. Only this directory is public now; app/, config/ and
 * database/ sit above it and cannot be requested over HTTP.
 */

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\BrowseController;
use App\Controllers\ChatController;
use App\Controllers\DashboardController;
use App\Controllers\HomeController;
use App\Controllers\PreferenceController;
use App\Controllers\ProfileController;
use App\Controllers\RequestController;
use App\Core\Router;

/** @var App\Core\Request $request */
$request = require dirname(__DIR__) . '/app/bootstrap.php';

$router = new Router();

// ------------------------------------------------------------------- public --
$router->get('/', [HomeController::class, 'index']);
$router->get('/about', [HomeController::class, 'about']);
$router->get('/stories', [HomeController::class, 'stories']);
$router->get('/help', [HomeController::class, 'help']);
$router->get('/privacy', [HomeController::class, 'privacy']);
$router->get('/terms', [HomeController::class, 'terms']);

$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->post('/logout', [AuthController::class, 'logout']);

// ------------------------------------------------------------------ members --
$router->get('/dashboard', [DashboardController::class, 'index']);

$router->get('/profile', [ProfileController::class, 'edit']);
$router->post('/profile', [ProfileController::class, 'update']);
$router->post('/profile/photo', [ProfileController::class, 'updatePhoto']);
$router->get('/members/{id}', [ProfileController::class, 'show']);

$router->get('/preferences', [PreferenceController::class, 'edit']);
$router->post('/preferences', [PreferenceController::class, 'update']);

$router->get('/browse', [BrowseController::class, 'index']);

$router->get('/requests', [RequestController::class, 'index']);
$router->post('/requests/send', [RequestController::class, 'send']);
$router->post('/requests/respond', [RequestController::class, 'respond']);
$router->post('/requests/cancel', [RequestController::class, 'cancel']);

$router->get('/matches', [ChatController::class, 'index']);
$router->get('/chat/{id}', [ChatController::class, 'show']);
$router->get('/chat/{id}/messages', [ChatController::class, 'fetch']);
$router->post('/chat/{id}/messages', [ChatController::class, 'send']);

// ------------------------------------------------------------------- admin ---
$router->get('/admin/login', [AdminController::class, 'showLogin']);
$router->post('/admin/login', [AdminController::class, 'login']);
$router->get('/admin', [AdminController::class, 'dashboard']);
$router->get('/admin/users', [AdminController::class, 'users']);
$router->post('/admin/users/status', [AdminController::class, 'setStatus']);
$router->post('/admin/users/delete', [AdminController::class, 'deleteUser']);
$router->post('/admin/logout', [AdminController::class, 'logout']);

$router->dispatch($request);
