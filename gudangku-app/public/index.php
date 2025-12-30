<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Tampilkan error saat dev
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Mulai session sekali saja
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Autoloader PSR-4 sederhana: App\* => /src/*
spl_autoload_register(function ($class) {
    $prefix  = 'App\\';
    $baseDir = __DIR__ . '/../src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

// Routing
$route = $_GET['r'] ?? 'login';

// Proteksi: halaman yang butuh login
$publicRoutes = ['login', 'category', 'product'];

use App\Controllers\AuthController;
use App\Controllers\CategoryController;
use App\Controllers\ProductController;
use App\Controllers\PrintController;  // TAMBAHKAN INI

if (!in_array($route, $publicRoutes) && empty($_SESSION['auth'])) {
    header('Location: /?r=login');
    exit;
}


switch ($route) {
    case 'login':
        $c = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $c->login();       // proses POST
        } else {
            $c->showLogin();   // tampilkan form
        }
        break;

    case 'logout':
        (new AuthController())->logout();
        break;

    case 'category':
        (new CategoryController())->index();
        break;

    case 'catCreate':
        (new CategoryController())->create();
        break;

    case 'catUpdate':
        (new CategoryController())->update();
        break;


    case 'product':
        (new ProductController())->index();
        break;

    case 'prodSave':
        (new ProductController())->save();
        break;

    case 'prodUpdate':
        (new ProductController())->update();
        break;


    // TAMBAHKAN CASE PRINT INI
    case 'print':
        PrintController::printProductsByCategory();
        break;

    default:
        http_response_code(404);
        echo "Page not found!";
}
