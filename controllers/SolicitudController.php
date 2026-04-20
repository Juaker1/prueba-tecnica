<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/Solicitud.php';

/**
 * Controlador de solicitudes.
 *
 * Acciones disponibles:
 *   - index()  → Lista solicitudes con filtros, diferenciada por rol.
 *   - create() → Muestra el formulario de nueva solicitud (solo rol usuario).
 *   - store()  → Procesa el POST del formulario y guarda en BD (solo rol usuario).
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
        $filtros = [
            'estado'   => $_GET['estado']   ?? '',
            'tipo'     => $_GET['tipo']      ?? '',
            'busqueda' => trim($_GET['busqueda'] ?? ''),
        ];

        $solicitudes = $this->solicitudModel->getAll($filtros, $usuarioId);
        $stats       = $this->solicitudModel->getStats($usuarioId);

        require_once __DIR__ . '/../views/solicitudes/index.php';
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
