<?php
$pageTitle = 'Nueva solicitud';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-7">

        <div class="d-flex align-items-center justify-content-between mb-4">
            <h1 class="h4 fw-semibold mb-0">
                <i class="bi bi-plus-circle text-primary me-2"></i>Nueva solicitud
            </h1>
            <a href="index.php?controller=solicitud&action=index" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Volver al listado
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-4">

                <form method="POST" action="index.php?controller=solicitud&action=store" novalidate id="createForm">

                    <div class="mb-3">
                        <label for="nombre_solicitante" class="form-label">
                            Nombre completo <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            class="form-control <?= isset($formErrors['nombre_solicitante']) ? 'is-invalid' : '' ?>"
                            id="nombre_solicitante"
                            name="nombre_solicitante"
                            value="<?= htmlspecialchars($formData['nombre_solicitante'] ?? $_SESSION['user_nombre']) ?>"
                            maxlength="100"
                            required
                        >
                        <?php if (isset($formErrors['nombre_solicitante'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($formErrors['nombre_solicitante']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="correo" class="form-label">
                            Correo electrónico <span class="text-danger">*</span>
                        </label>
                        <input
                            type="email"
                            class="form-control <?= isset($formErrors['correo']) ? 'is-invalid' : '' ?>"
                            id="correo"
                            name="correo"
                            value="<?= htmlspecialchars($formData['correo'] ?? $_SESSION['user_email']) ?>"
                            maxlength="150"
                            required
                        >
                        <?php if (isset($formErrors['correo'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($formErrors['correo']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="tipo" class="form-label">
                            Tipo de solicitud <span class="text-danger">*</span>
                        </label>
                        <select
                            class="form-select <?= isset($formErrors['tipo']) ? 'is-invalid' : '' ?>"
                            id="tipo"
                            name="tipo"
                            required
                        >
                            <option value="">— Selecciona una opción —</option>
                            <?php foreach (Solicitud::TIPOS as $valor => $etiqueta): ?>
                                <option
                                    value="<?= $valor ?>"
                                    <?= ($formData['tipo'] ?? '') === $valor ? 'selected' : '' ?>
                                >
                                    <?= htmlspecialchars($etiqueta) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($formErrors['tipo'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($formErrors['tipo']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label for="descripcion" class="form-label">
                            Descripción <span class="text-danger">*</span>
                        </label>
                        <textarea
                            class="form-control <?= isset($formErrors['descripcion']) ? 'is-invalid' : '' ?>"
                            id="descripcion"
                            name="descripcion"
                            rows="5"
                            required
                        ><?= htmlspecialchars($formData['descripcion'] ?? '') ?></textarea>
                        <?php if (isset($formErrors['descripcion'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($formErrors['descripcion']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-2"></i>Enviar solicitud
                        </button>
                        <a href="index.php?controller=solicitud&action=index" class="btn btn-outline-secondary">
                            Cancelar
                        </a>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
