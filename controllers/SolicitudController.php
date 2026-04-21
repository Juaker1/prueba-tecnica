<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/Solicitud.php';

/**
 * Controlador de solicitudes.
 *
 * Acciones disponibles:
 *   - index()        → Lista solicitudes con filtros, diferenciada por rol.
 *   - detail()       → Muestra el detalle de una solicitud (admin ve todas; usuario solo las propias).
 *   - updateEstado() → Actualiza estado y comentario de una solicitud (solo admin).
 *   - create()       → Muestra el formulario de nueva solicitud (solo rol usuario).
 *   - store()        → Procesa el POST del formulario y guarda en BD (solo rol usuario).
 */
class SolicitudController
{
    private Solicitud $solicitudModel;

    public function __construct()
    {
        $this->solicitudModel = new Solicitud();
    }

    /**
     * Lista solicitudes con filtros opcionales y estadísticas por rol.
     * Admin ve todas; usuario ve solo las suyas.
     */
    public function index(): void
    {
        requireLogin();

        $esAdmin    = $_SESSION['user_rol'] === 'admin';
        $usuarioId  = $esAdmin ? null : (int) $_SESSION['user_id'];

        // Extraer y sanear filtros desde GET
        // La búsqueda por texto solo aplica al admin (el usuario solo ve sus propias solicitudes)
        $filtros = [
            'estado'   => $_GET['estado']   ?? '',
            'tipo'     => $_GET['tipo']      ?? '',
            'busqueda' => $esAdmin ? trim($_GET['busqueda'] ?? '') : '',
        ];

        $solicitudes = $this->solicitudModel->getAll($filtros, $usuarioId);
        $stats       = $this->solicitudModel->getStats($usuarioId);

        require_once __DIR__ . '/../views/solicitudes/index.php';
    }

    /**
     * Muestra el detalle completo de una solicitud.
     * Admin ve todas; usuario solo las propias.
     */
    public function detail(): void
    {
        requireLogin();

        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            http_response_code(404);
            die('Solicitud no encontrada.');
        }

        $solicitud = $this->solicitudModel->findById($id);

        if (!$solicitud) {
            http_response_code(404);
            die('Solicitud no encontrada.');
        }

        $esAdmin = $_SESSION['user_rol'] === 'admin';

        // Usuario solo puede ver sus propias solicitudes
        if (!$esAdmin && (int) $solicitud['usuario_id'] !== (int) $_SESSION['user_id']) {
            $_SESSION['flash_error'] = 'No tienes permisos para ver esa solicitud.';
            header('Location: index.php?controller=solicitud&action=index');
            exit;
        }

        require_once __DIR__ . '/../views/solicitudes/detail.php';
    }

    /**
     * Procesa el formulario de cambio de estado (solo admin).
     * Actualiza estado y comentario opcional en la BD.
     */
    public function updateEstado(): void
    {
        requireLogin();
        requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=solicitud&action=index');
            exit;
        }

        $id         = (int) ($_POST['id'] ?? 0);
        $estado     =        $_POST['estado']     ?? '';
        $comentario = trim(  $_POST['comentario'] ?? '') ?: null;

        if ($id <= 0 || !array_key_exists($estado, Solicitud::ESTADOS)) {
            $_SESSION['flash_error'] = 'Datos inválidos.';
            header('Location: index.php?controller=solicitud&action=index');
            exit;
        }

        $solicitud = $this->solicitudModel->findById($id);

        if (!$solicitud) {
            $_SESSION['flash_error'] = 'Solicitud no encontrada.';
            header('Location: index.php?controller=solicitud&action=index');
            exit;
        }

        $this->solicitudModel->updateEstado($id, $estado, $comentario);

        $_SESSION['flash_success'] = 'Estado actualizado correctamente.';
        header('Location: index.php?controller=solicitud&action=detail&id=' . $id);
        exit;
    }

    /**
     * Muestra el formulario para crear una nueva solicitud.
     * Solo accesible para rol 'usuario'.
     */
    public function create(): void
    {
        requireLogin();
        requireRole('usuario');

        $formData   = [];
        $formErrors = [];

        require_once __DIR__ . '/../views/solicitudes/create.php';
    }

    /**
     * Recibe el POST del formulario, valida y guarda la solicitud.
     * Solo accesible para rol 'usuario'.
     */
    public function store(): void
    {
        requireLogin();
        requireRole('usuario');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=solicitud&action=create');
            exit;
        }

        $nombre      = trim($_POST['nombre_solicitante'] ?? '');
        $correo      = trim($_POST['correo']             ?? '');
        $tipo        =      $_POST['tipo']               ?? '';
        $descripcion = trim($_POST['descripcion']        ?? '');

        $formErrors = [];

        if ($nombre === '') {
            $formErrors['nombre_solicitante'] = 'El nombre es obligatorio.';
        }

        if ($correo === '') {
            $formErrors['correo'] = 'El correo es obligatorio.';
        } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $formErrors['correo'] = 'El formato del correo no es válido.';
        }

        if (!array_key_exists($tipo, Solicitud::TIPOS)) {
            $formErrors['tipo'] = 'Selecciona un tipo de solicitud válido.';
        }

        if ($descripcion === '') {
            $formErrors['descripcion'] = 'La descripción es obligatoria.';
        }

        // Si hay errores, volver al formulario conservando los datos ingresados
        if (!empty($formErrors)) {
            $formData = $_POST;
            require_once __DIR__ . '/../views/solicitudes/create.php';
            return;
        }

        $this->solicitudModel->create([
            'usuario_id'         => (int) $_SESSION['user_id'],
            'nombre_solicitante' => $nombre,
            'correo'             => $correo,
            'tipo'               => $tipo,
            'descripcion'        => $descripcion,
        ]);

        $_SESSION['flash_success'] = 'Solicitud creada exitosamente.';
        header('Location: index.php?controller=solicitud&action=index');
        exit;
    }
}
