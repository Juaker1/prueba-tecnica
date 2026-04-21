<?php

declare(strict_types=1);

/**
 * Configuración de la conexión a la base de datos.
 *
 * Retorna una instancia PDO configurada con:
 *  - Modo de errores en excepciones para manejo explícito.
 *  - Modo de fetch como arreglo asociativo por defecto.
 *  - Emulación de prepared statements desactivada (seguridad).
 *  - Charset utf8mb4 para soporte completo de Unicode.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'prueba_tecnica');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

function getDB(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_NAME,
            DB_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // En producción, loguear el error real y mostrar mensaje genérico.
            error_log('DB connection error: ' . $e->getMessage());
            http_response_code(500);
            die(json_encode(['error' => 'Error de conexión a la base de datos.']));
        }
    }

    return $pdo;
}
