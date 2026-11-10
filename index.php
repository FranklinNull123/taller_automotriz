<?php
require_once __DIR__ . '/includes/auth.php';

requireAuth();

$permisosModulo = [
    'clientes' => ['admin', 'recepcion'],
    'ordenes' => ['admin', 'recepcion', 'mecanico'],
    'inventario' => ['admin', 'recepcion'],
    'garantias' => ['admin', 'recepcion'],
    'reportes' => ['admin', 'recepcion']
];

$modulo = isset($_GET['modulo']) ? $_GET['modulo'] : 'inicio';
if (isset($permisosModulo[$modulo])) {
    requireRole($permisosModulo[$modulo]);
}

require_once 'views/layout/header.php';

switch ($modulo) {
    case 'clientes':
        require_once 'views/clientes/index.php';
        break;
    case 'ordenes':
        require_once 'views/ordenes/index.php';
        break;
    case 'inventario':
        require_once 'views/inventario/index.php';
        break;
    case 'garantias':
        require_once 'views/garantias/index.php';
        break;
    case 'reportes':
        require_once 'views/reportes/index.php';
        break;
    default:
        require_once 'models/ReporteModel.php';
        $dashboard = (new ReporteModel())->obtenerDashboard();
        $estadisticas = $dashboard['estadisticas'];
        $porEstado = $dashboard['por_estado'];
        ?>
        <div class="dashboard-hero text-white p-4 p-md-5 mb-4 shadow-sm">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <span class="badge bg-danger-subtle text-danger mb-2">Resumen operativo</span>
                    <h2 class="fw-bold mb-2">Panel de control</h2>
                    <p class="text-white-50 mb-0">Consulta el estado del taller y accede rápidamente a cada módulo.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <i class="bi bi-speedometer2 display-3 text-white-50"></i>
                </div>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-xl-3">
                <div class="card kpi-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="kpi-icon bg-primary-subtle text-primary"><i class="bi bi-clipboard2-check"></i></span>
                            <span class="text-muted small">Este mes</span>
                        </div>
                        <div class="text-muted small">Órdenes atendidas</div>
                        <div class="fs-2 fw-bold"><?= (int) $estadisticas['ordenes_mes'] ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card kpi-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="kpi-icon bg-warning-subtle text-warning"><i class="bi bi-car-front"></i></span>
                            <span class="text-muted small">Activas</span>
                        </div>
                        <div class="text-muted small">Vehículos en taller</div>
                        <div class="fs-2 fw-bold"><?= (int) $estadisticas['en_taller'] ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card kpi-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="kpi-icon bg-success-subtle text-success"><i class="bi bi-cash-stack"></i></span>
                            <span class="text-muted small">Este mes</span>
                        </div>
                        <div class="text-muted small">Ingresos registrados</div>
                        <div class="fs-2 fw-bold">S/ <?= htmlspecialchars(number_format((float) $estadisticas['ingresos'], 2), ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card kpi-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="kpi-icon bg-danger-subtle text-danger"><i class="bi bi-exclamation-triangle"></i></span>
                            <span class="text-muted small">Atención</span>
                        </div>
                        <div class="text-muted small">Stock bajo / garantías</div>
                        <div class="fs-2 fw-bold"><?= (int) $dashboard['stock_bajo'] + (int) $dashboard['garantias_por_vencer'] ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-4 mt-1">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div><h5 class="fw-bold mb-1">Órdenes por estado</h5><small class="text-muted">Distribución actual del taller.</small></div>
                            <i class="bi bi-bar-chart-line text-danger fs-4"></i>
                        </div>
                        <canvas id="ordenesPorEstado" height="135"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Accesos rápidos</h5>
                        <a href="?modulo=clientes" class="btn btn-light border w-100 text-start mb-2"><i class="bi bi-people text-primary me-2"></i>Gestionar clientes</a>
                        <a href="?modulo=ordenes" class="btn btn-light border w-100 text-start mb-2"><i class="bi bi-tools text-danger me-2"></i>Revisar órdenes</a>
                        <a href="?modulo=inventario" class="btn btn-light border w-100 text-start mb-2"><i class="bi bi-box-seam text-success me-2"></i>Ver inventario</a>
                        <a href="?modulo=garantias" class="btn btn-light border w-100 text-start"><i class="bi bi-shield-check text-warning me-2"></i>Consultar garantías</a>
                    </div>
                </div>
            </div>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (!window.Chart) return;
            new Chart(document.getElementById('ordenesPorEstado'), {
                type: 'doughnut',
                data: {
                    labels: <?= json_encode(array_keys($porEstado), JSON_UNESCAPED_UNICODE) ?>,
                    datasets: [{
                        data: <?= json_encode(array_values($porEstado)) ?>,
                        backgroundColor: ['#f59f00', '#0ea5e9', '#2fb344', '#64748b'],
                        borderWidth: 0
                    }]
                },
                options: { plugins: { legend: { position: 'bottom' } }, cutout: '68%' }
            });
        });
        </script>
        <?php
        break;
}

require_once 'views/layout/footer.php';
?>