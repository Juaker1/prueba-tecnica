<?php

declare(strict_types=1);

/**
 * Funciones de control de acceso.
 *
 * Actúan como middleware: se llaman al inicio de cada acción del controlador
 * para verificar autenticación y permisos antes de ejecutar cualquier lógica.
 */

/**
 * Verifica que el usuario esté autenticado.
 *
 * Si no hay sesión activa, redirige al login y detiene la ejecución.
 * Llamar al inicio de cualquier acción que requiera estar logueado.
 */
function requireLogin(): void
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?controller=auth&action=login');
        exit;
    }
}

/**
 * Verifica que el usuario autenticado tenga el rol requerido.
 *
 * Debe llamarse siempre DESPUÉS de requireLogin().
 * Si el rol no coincide, redirige al listado con un mensaje de acceso denegado.
 *
 * @param string $rol  Rol requerido: 'admin' o 'usuario'.
 */
function requireRole(string $rol): void
{
    if (($_SESSION['user_rol'] ?? '') !== $rol) {
        $_SESSION['flash_error'] = 'No tienes permisos para realizar esa acción.';
        header('Location: index.php?controller=solicitud&action=index');
        exit;
    }
}
