<?php

declare(strict_types=1);

/**
 * Modelo Solicitud.
 *
 * Responsable de todas las operaciones de base de datos
 * relacionadas con la tabla `solicitudes`.
 */
class Solicitud
{
    private PDO $pdo;

    /** Tipos válidos según el ENUM de la tabla. */
    public const TIPOS = [
        'academica'          => 'Solicitud académica',
        'certificado'        => 'Certificado',
        'actualizacion_datos'=> 'Actualización de datos',
        'otra'               => 'Otra',
    ];

    /** Estados válidos según el ENUM de la tabla. */
    public const ESTADOS = [
        'pendiente'   => 'Pendiente',
        'en_revision' => 'En revisión',
        'aprobada'    => 'Aprobada',
        'rechazada'   => 'Rechazada',
    ];

    public function __construct()
    {
        $this->pdo = getDB();
    }

    /**
     * Inserta una nueva solicitud en la base de datos.
     * Retorna el ID generado.
     *
     * @param array{
     *   usuario_id: int,
     *   nombre_solicitante: string,
     *   correo: string,
     *   tipo: string,
     *   descripcion: string
     * } $data
     */
    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO solicitudes (usuario_id, nombre_solicitante, correo, tipo, descripcion)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['usuario_id'],
            $data['nombre_solicitante'],
            $data['correo'],
            $data['tipo'],
            $data['descripcion'],
        ]);
        return (int) $this->pdo->lastInsertId();
    }
}
