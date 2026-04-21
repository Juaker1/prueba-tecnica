<?php

/**
 * Script de datos iniciales (seed).
 *
 * Inserta los usuarios de prueba con contraseñas hasheadas correctamente.
 * Ejecutar UNA VEZ después de importar schema.sql:
 *
 *   php database/seed.php
 *
 * Usuarios creados:
 *   admin@universidad.cl   → Admin1234    (rol: admin)
 *   juan@universidad.cl    → Usuario1234  (rol: usuario)
 *   maria@universidad.cl   → Usuario1234  (rol: usuario)
 *   carlos@universidad.cl  → Usuario1234  (rol: usuario)
 *   ana@universidad.cl     → Usuario1234  (rol: usuario)
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

$usuarios = [
    [
        'nombre'   => 'Administrador',
        'email'    => 'admin@universidad.cl',
        'password' => 'Admin1234',
        'rol'      => 'admin',
    ],
    [
        'nombre'   => 'Juan Pérez',
        'email'    => 'juan@universidad.cl',
        'password' => 'Usuario1234',
        'rol'      => 'usuario',
    ],
    [
        'nombre'   => 'María González',
        'email'    => 'maria@universidad.cl',
        'password' => 'Usuario1234',
        'rol'      => 'usuario',
    ],
    [
        'nombre'   => 'Carlos Fuentes',
        'email'    => 'carlos@universidad.cl',
        'password' => 'Usuario1234',
        'rol'      => 'usuario',
    ],
    [
        'nombre'   => 'Ana Ramírez',
        'email'    => 'ana@universidad.cl',
        'password' => 'Usuario1234',
        'rol'      => 'usuario',
    ],
];

$pdo  = getDB();
$stmt = $pdo->prepare(
    'INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)'
);

foreach ($usuarios as $usuario) {
    $hash = password_hash($usuario['password'], PASSWORD_BCRYPT);

    try {
        $stmt->execute([$usuario['nombre'], $usuario['email'], $hash, $usuario['rol']]);
        echo "Usuario creado: {$usuario['email']}\n";
    } catch (PDOException $e) {
        // El email ya existe (UNIQUE constraint) u otro error
        echo "Omitido {$usuario['email']}: " . $e->getMessage() . "\n";
    }
}

echo "Seed completado.\n";
