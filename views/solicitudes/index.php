<?php
$pageTitle = 'Solicitudes';
require_once __DIR__ . '/../layouts/header.php';

// Determina si hay algún filtro activo (para el botón limpiar)
$hayFiltros = !empty($filtros['busqueda']) || !empty($filtros['estado']) || !empty($filtros['tipo']);

// Colores de badge por estado
$badgeEstado = [
    'pendiente'   => 'warning text-dark',
    'en_revision' => 'info text-dark',
    'aprobada'    => 'success',
    'rechazada'   => 'danger',
];
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h4 fw-semibold mb-0">
        <i class="bi bi-list-ul text-primary me-2"></i>
        <?= $esAdmin ? 'Todas las solicitudes' : 'Mis solicitudes' ?>
    </h1>
    <?php if (!$esAdmin): ?>
        <a href="index.php?controller=solicitud&action=create" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i>Nueva solicitud
        </a>
    <?php endif; ?>
</div>

<!-- ── TARJETAS DE ESTADÍSTICAS ───────────────────────────── -->
<div class="row g-3 mb-4">
    <?php
    $totalSolicitudes = array_sum($stats);
    $tarjetas = $esAdmin
        ? [
            ['label' => 'Total',        'valor' => $totalSolicitudes,                         'color' => 'primary', 'icon' => 'journal-text'],
            ['label' => 'Pendientes',   'valor' => $stats['pendiente'],                       'color' => 'warning', 'icon' => 'clock'],
            ['label' => 'En revisión',  'valor' => $stats['en_revision'],                     'color' => 'info',    'icon' => 'search'],
            ['label' => 'Resueltas',    'valor' => $stats['aprobada'] + $stats['rechazada'],  'color' => 'success', 'icon' => 'check-circle'],
          ]
        : [
            ['label' => 'Mis solicitudes', 'valor' => $totalSolicitudes,                      'color' => 'primary', 'icon' => 'journal-text'],
            ['label' => 'Pendientes',      'valor' => $stats['pendiente'],                    'color' => 'warning', 'icon' => 'clock'],
            ['label' => 'En revisión',     'valor' => $stats['en_revision'],                  'color' => 'info',    'icon' => 'search'],
            ['label' => 'Aprobadas',       'valor' => $stats['aprobada'],                     'color' => 'success', 'icon' => 'check-circle'],
          ];
    ?>
    <?php foreach ($tarjetas as $t): ?>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-<?= $t['color'] ?> bg-opacity-10 p-3">
                    <i class="bi bi-<?= $t['icon'] ?> fs-4 text-<?= $t['color'] ?>"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bold lh-1"><?= $t['valor'] ?></div>
                    <div class="text-muted small"><?= $t['label'] ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- ── PANEL DE FILTROS ───────────────────────────────────── -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="index.php" class="row g-2 align-items-end" id="filtroForm">
            <input type="hidden" name="controller" value="solicitud">
            <input type="hidden" name="action" value="index">

            <div class="col-12 col-md-4">
                <label class="form-label small fw-semibold mb-1">Buscar</label>
                <input
                    type="text"
                    name="busqueda"
                    class="form-control form-control-sm"
                    placeholder="Nombre o correo..."
                    value="<?= htmlspecialchars($filtros['busqueda']) ?>"
                >
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label small fw-semibold mb-1">Estado</label>
                <select name="estado" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <?php foreach (Solicitud::ESTADOS as $valor => $etiqueta): ?>
                        <option value="<?= $valor ?>" <?= $filtros['estado'] === $valor ? 'selected' : '' ?>>
                            <?= $etiqueta ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label small fw-semibold mb-1">Tipo</label>
                <select name="tipo" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <?php foreach (Solicitud::TIPOS as $valor => $etiqueta): ?>
                        <option value="<?= $valor ?>" <?= $filtros['tipo'] === $valor ? 'selected' : '' ?>>
                            <?= $etiqueta ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-search me-1"></i>Filtrar
                </button>
                <a
                    id="btnLimpiar"
                    href="index.php?controller=solicitud&action=index"
                    class="btn btn-outline-secondary btn-sm <?= !$hayFiltros ? 'disabled' : '' ?>"
                    title="Limpiar filtros"
                    <?= !$hayFiltros ? 'tabindex="-1" aria-disabled="true"' : '' ?>
                >
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- ── TABLA DE SOLICITUDES ──────────────────────────────── -->
<?php if (empty($solicitudes)): ?>
    <div class="text-center text-muted py-5">
        <i class="bi bi-inbox display-4 d-block mb-3"></i>
        No se encontraron solicitudes<?= (!empty($filtros['busqueda']) || !empty($filtros['estado']) || !empty($filtros['tipo'])) ? ' con los filtros aplicados' : '' ?>.
    </div>
<?php else: ?>
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">#</th>
                    <th>Solicitante</th>
                    <?php if ($esAdmin): ?>
                    <th>Correo</th>
                    <?php endif; ?>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th class="text-end pe-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($solicitudes as $s): ?>
                <tr>
                    <td class="ps-3 text-muted small"><?= $s['id'] ?></td>
                    <td class="fw-semibold"><?= htmlspecialchars($s['nombre_solicitante']) ?></td>
                    <?php if ($esAdmin): ?>
                    <td class="text-muted small"><?= htmlspecialchars($s['correo']) ?></td>
                    <?php endif; ?>
                    <td><?= htmlspecialchars(Solicitud::TIPOS[$s['tipo']] ?? $s['tipo']) ?></td>
                    <td>
                        <span class="badge bg-<?= $badgeEstado[$s['estado']] ?? 'secondary' ?>">
                            <?= htmlspecialchars(Solicitud::ESTADOS[$s['estado']] ?? $s['estado']) ?>
                        </span>
                    </td>
                    <td class="text-muted small">
                        <?= date('d/m/Y', strtotime($s['created_at'])) ?>
                    </td>
                    <td class="text-end pe-3">
                        <a
                            href="index.php?controller=solicitud&action=detail&id=<?= $s['id'] ?>"
                            class="btn btn-outline-primary btn-sm"
                        >
                            <i class="bi bi-eye me-1"></i>Ver
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="text-muted small mt-2 text-end">
    <?= count($solicitudes) ?> resultado<?= count($solicitudes) !== 1 ? 's' : '' ?>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
