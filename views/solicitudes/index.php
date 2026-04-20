<?php
$pageTitle = 'Solicitudes';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h4 fw-semibold mb-0">
        <i class="bi bi-list-ul text-primary me-2"></i>Solicitudes
    </h1>
    <?php if ($_SESSION['user_rol'] === 'usuario'): ?>
        <a href="index.php?controller=solicitud&action=create" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i>Nueva solicitud
        </a>
    <?php endif; ?>
</div>

<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>
    No hay ninguna solicitud registrada aún. Utiliza el botón "Nueva solicitud" para crear una.
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
