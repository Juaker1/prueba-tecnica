<?php

/**
 * Punto de entrada único de la aplicación (Front Controller).
 *
 * Todas las peticiones pasan por aquí. El enrutamiento se resuelve
 * mediante los parámetros GET `controller` y `action`, con valores
 * por defecto para la página de inicio (login).
 *
 * Ejemplo de URLs:
 *   index.php                               → login
 *   index.php?controller=auth&action=login  → AuthController::login()
 *   index.php?controller=solicitud&action=index → SolicitudController::index()
 */

declare(strict_types=1);

session_start();

require_once __DIR__ . '/config/database.php';

// URL base para referenciar assets desde las vistas
define('BASE_URL', '/prueba-tecnica/');

// --- Resolución del controlador y acción ---
$controllerName = $_GET['controller'] ?? 'auth';
$actionName     = $_GET['action']     ?? 'login';

// Whitelist de controladores permitidos para evitar path traversal
$allowedControllers = ['auth', 'solicitud'];

if (!in_array($controllerName, $allowedControllers, true)) {
    http_response_code(404);
    die('Página no encontrada.');
}

$controllerFile  = __DIR__ . '/controllers/' . ucfirst($controllerName) . 'Controller.php';

if (!file_exists($controllerFile)) {
    http_response_code(404);
    die('Controlador no encontrado.');
}

require_once $controllerFile;

$controllerClass = ucfirst($controllerName) . 'Controller';

if (!class_exists($controllerClass)) {
    http_response_code(500);
    die('Error interno del servidor.');
}

$controller = new $controllerClass();

// Sanitizar el nombre de la acción: solo letras y números
$actionName = preg_replace('/[^a-zA-Z0-9]/', '', $actionName);

if (!method_exists($controller, $actionName)) {
    http_response_code(404);
    die('Acción no encontrada.');
}

$controller->$actionName();
