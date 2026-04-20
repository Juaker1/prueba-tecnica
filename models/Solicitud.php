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
     * Retorna solicitudes aplicando filtros opcionales.
     *
     * Si se pasa $usuarioId, restringe los resultados a ese usuario (rol usuario).
     * Si es null, trae todas (rol admin).
     *
     * @param array{estado?: string, tipo?: string, busqueda?: string} $filtros
     */
    public function getAll(array $filtros = [], ?int $usuarioId = null): array
    {
        $conditions = [];
        $params     = [];

        if ($usuarioId !== null) {
            $conditions[] = 'usuario_id = ?';
            $params[]     = $usuarioId;
        }

        if (!empty($filtros['estado']) && array_key_exists($filtros['estado'], self::ESTADOS)) {
            $conditions[] = 'estado = ?';
            $params[]     = $filtros['estado'];
        }

        if (!empty($filtros['tipo']) && array_key_exists($filtros['tipo'], self::TIPOS)) {
            $conditions[] = 'tipo = ?';
            $params[]     = $filtros['tipo'];
        }

        if (!empty($filtros['busqueda'])) {
            $conditions[] = '(nombre_solicitante LIKE ? OR correo LIKE ?)';
            $termino      = '%' . $filtros['busqueda'] . '%';
            $params[]     = $termino;
            $params[]     = $termino;
        }

        $sql = 'SELECT id, nombre_solicitante, correo, tipo, estado, created_at
                FROM solicitudes';

        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= ' ORDER BY created_at DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Retorna conteos de solicitudes agrupados por estado.
     * Si se pasa $usuarioId, limita las estadísticas a ese usuario.
     *
     * @return array<string, int>  ['pendiente' => 3, 'aprobada' => 1, ...]
     */
    public function getStats(?int $usuarioId = null): array
    {
        $stats = array_fill_keys(array_keys(self::ESTADOS), 0);

        $sql    = 'SELECT estado, COUNT(*) AS total FROM solicitudes';
        $params = [];

        if ($usuarioId !== null) {
            $sql   .= ' WHERE usuario_id = ?';
            $params[] = $usuarioId;
        }

        $sql .= ' GROUP BY estado';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        foreach ($stmt->fetchAll() as $row) {
            $stats[$row['estado']] = (int) $row['total'];
        }

        return $stats;
    }

    /**
     * Busca una solicitud por su ID.
     * Retorna el array completo o false si no existe.
     */
    public function findById(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            'SELECT s.*, u.nombre AS nombre_usuario
             FROM solicitudes s
             INNER JOIN usuarios u ON u.id = s.usuario_id
             WHERE s.id = ?
             LIMIT 1'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Actualiza el estado y el comentario opcional de una solicitud.
     * Solo el administrador puede llamar a este método.
     */
    public function updateEstado(int $id, string $estado, ?string $comentario): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE solicitudes
             SET estado = ?, comentario = ?
             WHERE id = ?'
        );
        return $stmt->execute([$estado, $comentario, $id]);
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
