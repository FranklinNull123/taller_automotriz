<?php
session_start();

if (!isset($_SESSION['acceso'])) {
    header("Location: login.php");
    exit();
}

require_once 'views/layout/header.php';

$modulo = isset($_GET['modulo']) ? $_GET['modulo'] : 'inicio';

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
        // Mantén el HTML de tus 3 tarjetas de bienvenida aquí abajo
        echo '
        <div class="text-center mb-5">
            <h2 class="fw-bold text-dark">Panel de Control</h2>
            <p class="text-muted">Selecciona un módulo para gestionar las operaciones del taller</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <a href="?modulo=clientes" class="text-decoration-none">
                    <div class="card h-100 border bg-white shadow-sm card-dashboard">
                        <div class="card-body text-center p-4">
                            <div class="text-primary mb-3"><i class="bi bi-people icon-lg"></i></div>
                            <h5 class="fw-bold text-dark">Clientes y Vehículos</h5>
                            <p class="text-muted small mb-0">Directorio de clientes y registro de placas.</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="?modulo=ordenes" class="text-decoration-none">
                    <div class="card h-100 border bg-white shadow-sm card-dashboard">
                        <div class="card-body text-center p-4">
                            <div class="text-danger mb-3"><i class="bi bi-clipboard2-pulse icon-lg"></i></div>
                            <h5 class="fw-bold text-dark">Órdenes de Servicio</h5>
                            <p class="text-muted small mb-0">Diagnósticos, reparaciones y estados de entrega.</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="?modulo=inventario" class="text-decoration-none">
                    <div class="card h-100 border bg-white shadow-sm card-dashboard">
                        <div class="card-body text-center p-4">
                            <div class="text-success mb-3"><i class="bi bi-boxes icon-lg"></i></div>
                            <h5 class="fw-bold text-dark">Repuestos e Inventario</h5>
                            <p class="text-muted small mb-0">Control de stock y piezas del taller.</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>';
        break;
}

require_once 'views/layout/footer.php';
?>