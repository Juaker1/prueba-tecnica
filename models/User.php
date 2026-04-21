<?php

declare(strict_types=1);

/**
 * Modelo User.
 *
 * Responsable de todas las operaciones de base de datos
 * relacionadas con la tabla `usuarios`.
 */
class User
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getDB();
    }

    /**
     * Busca un usuario por su email.
     * Retorna el array con los datos del usuario o false si no existe.
     */
    public function findByEmail(string $email): array|false
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, nombre, email, password, rol
             FROM usuarios
             WHERE email = ?
             LIMIT 1'
        );
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    /**
     * Busca un usuario por su ID.
     * Útil para verificar sesiones activas.
     */
    public function findById(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, nombre, email, rol
             FROM usuarios
             WHERE id = ?
             LIMIT 1'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
