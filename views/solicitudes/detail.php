<?php
$pageTitle = 'Detalle de solicitud #' . $solicitud['id'];
require_once __DIR__ . '/../layouts/header.php';

$badgeEstado = [
    'pendiente'   => 'warning text-dark',
    'en_revision' => 'info text-dark',
    'aprobada'    => 'success',
    'rechazada'   => 'danger',
];
?>

<!-- Encabezado -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h4 fw-semibold mb-0">
        <i class="bi bi-file-text text-primary me-2"></i>
        Solicitud <span class="text-muted fw-normal">#<?= $solicitud['id'] ?></span>
    </h1>
    <a href="index.php?controller=solicitud&action=index" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver al listado
    </a>
</div>

<div class="row g-4">

    <!-- ── COLUMNA PRINCIPAL: detalle de la solicitud ───────── -->
    <div class="<?= $esAdmin ? 'col-lg-7' : 'col-12' ?>">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                <span class="fw-semibold">Información de la solicitud</span>
                <span class="badge bg-<?= $badgeEstado[$solicitud['estado']] ?? 'secondary' ?> fs-6">
                    <?= htmlspecialchars(Solicitud::ESTADOS[$solicitud['estado']] ?? $solicitud['estado']) ?>
                </span>
            </div>
            <div class="card-body">

                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted fw-normal">Solicitante</dt>
                    <dd class="col-sm-8 fw-semibold"><?= htmlspecialchars($solicitud['nombre_solicitante']) ?></dd>

                    <dt class="col-sm-4 text-muted fw-normal">Correo</dt>
                    <dd class="col-sm-8">
                        <a href="mailto:<?= htmlspecialchars($solicitud['correo']) ?>">
                            <?= htmlspecialchars($solicitud['correo']) ?>
                        </a>
                    </dd>

                    <dt class="col-sm-4 text-muted fw-normal">Tipo</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars(Solicitud::TIPOS[$solicitud['tipo']] ?? $solicitud['tipo']) ?></dd>

                    <dt class="col-sm-4 text-muted fw-normal">Fecha de creación</dt>
                    <dd class="col-sm-8 text-muted small">
                        <?= date('d/m/Y H:i', strtotime($solicitud['created_at'])) ?>
                    </dd>

                    <dt class="col-sm-4 text-muted fw-normal">Última actualización</dt>
                    <dd class="col-sm-8 text-muted small">
                        <?= date('d/m/Y H:i', strtotime($solicitud['updated_at'])) ?>
                    </dd>

                    <?php if ($esAdmin): ?>
                    <dt class="col-sm-4 text-muted fw-normal">Usuario registrado</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($solicitud['nombre_usuario']) ?></dd>
                    <?php endif; ?>
                </dl>

                <hr>

                <p class="text-muted small mb-1 fw-semibold">Descripción</p>
                <p class="mb-0"><?= nl2br(htmlspecialchars($solicitud['descripcion'])) ?></p>

                <?php if (!empty($solicitud['comentario'])): ?>
                <hr>
                <p class="text-muted small mb-1 fw-semibold">
                    <i class="bi bi-chat-left-text me-1"></i>Comentario del administrador
                </p>
                <div class="p-3 bg-light rounded border-start border-4 border-primary">
                    <?= nl2br(htmlspecialchars($solicitud['comentario'])) ?>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <!-- ── COLUMNA DERECHA: gestión (solo admin) ────────────── -->
    <?php if ($esAdmin): ?>
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <span class="fw-semibold">
                    <i class="bi bi-pencil-square text-primary me-2"></i>Gestionar solicitud
                </span>
            </div>
            <div class="card-body">

                <form method="POST" action="index.php?controller=solicitud&action=updateEstado">
                    <input type="hidden" name="id" value="<?= $solicitud['id'] ?>">

                    <div class="mb-3">
                        <label for="estado" class="form-label fw-semibold">Estado</label>
                        <select class="form-select" id="estado" name="estado" required>
                            <?php foreach (Solicitud::ESTADOS as $valor => $etiqueta): ?>
                                <option
                                    value="<?= $valor ?>"
                                    <?= $solicitud['estado'] === $valor ? 'selected' : '' ?>
                                >
                                    <?= htmlspecialchars($etiqueta) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="comentario" class="form-label fw-semibold">
                            Comentario
                            <span class="text-muted fw-normal small">(opcional)</span>
                        </label>
                        <textarea
                            class="form-control"
                            id="comentario"
                            name="comentario"
                            rows="5"
                            placeholder="Agrega un comentario o retroalimentación para el solicitante..."
                        ><?= htmlspecialchars($solicitud['comentario'] ?? '') ?></textarea>
                        <div class="form-text">
                            El comentario es visible para el solicitante.
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-lg me-2"></i>Guardar cambios
                    </button>
                </form>

            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
