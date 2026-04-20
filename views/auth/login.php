<?php
$pageTitle = 'Iniciar sesión';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="row justify-content-center">
    <div class="col-sm-10 col-md-6 col-lg-4">

        <div class="text-center mb-4">
            <i class="bi bi-journal-text display-4 text-primary"></i>
            <h1 class="h4 mt-2 fw-semibold">Gestión de Solicitudes</h1>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h2 class="card-title h5 mb-4">Iniciar sesión</h2>

                <?php if ($error !== null): ?>
                    <div class="alert alert-danger d-flex align-items-center gap-2" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span><?= htmlspecialchars($error) ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" action="index.php?controller=auth&action=login" novalidate id="loginForm">

                    <div class="mb-3">
                        <label for="email" class="form-label">Correo electrónico</label>
                        <input
                            type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                            placeholder="usuario@universidad.cl"
                            required
                            autocomplete="email"
                        >
                        <div class="invalid-feedback">Ingresa un correo válido.</div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Contraseña</label>
                        <input
                            type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                        >
                        <div class="invalid-feedback">Ingresa tu contraseña.</div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Ingresar
                    </button>

                </form>
            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
