<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Taller Automotriz</title>
    <!-- Bootstrap CSS Local -->
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background: #f1f5f9; font-family: system-ui, -apple-system, sans-serif; color: #172033; }
        .navbar { background: linear-gradient(110deg, #111827, #1e293b 55%, #26364f) !important; padding: .85rem 0; }
        .navbar-brand { font-weight: 800; letter-spacing: 0.5px; font-size: 1.25rem; }
        .nav-link { font-weight: 500; padding: 0.55rem 1rem !important; border-radius: 0.65rem; transition: all 0.2s ease; color: #cbd5e1 !important; }
        .nav-link:hover, .nav-link.active { background-color: #334155; color: #ffffff !important; box-shadow: inset 0 -2px #ef3340; }
        .user-avatar { width: 28px; height: 28px; display: inline-grid; place-items: center; border-radius: 50%; background: #dc3545; color: #fff; font-size: .85rem; }
        .dropdown-menu { border-radius: .75rem; min-width: 190px; }
        .dropdown-item { border-radius: .45rem; margin: .15rem .35rem; width: auto; }
        .dropdown-item:hover { background: #fff1f2; color: #be123c !important; }
        
        /* Clases para las tarjetas del Dashboard principal */
        .card-dashboard { transition: transform 0.2s, box-shadow 0.2s; border-radius: 12px; }
        .card-dashboard:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; border-color: #cbd5e1 !important; }
        .icon-lg { font-size: 3rem; }
    </style>
</head>
<body>

<?php 
// Detectar el módulo actual para iluminar el menú
$moduloActual = isset($_GET['modulo']) ? $_GET['modulo'] : ''; 
?>

<nav class="navbar navbar-expand-lg navbar-dark shadow mb-5">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <i class="bi bi-car-front-fill me-2 text-danger fs-3"></i>
            <span>TALLER <span class="text-danger">AUTOMOTRIZ</span></span>
        </a>
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto gap-1">
                <li class="nav-item">
                    <a class="nav-link <?= $moduloActual == 'clientes' ? 'active' : '' ?>" href="?modulo=clientes"><i class="bi bi-people-fill me-1"></i> Clientes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $moduloActual == 'ordenes' ? 'active' : '' ?>" href="?modulo=ordenes"><i class="bi bi-tools me-1"></i> Órdenes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $moduloActual == 'inventario' ? 'active' : '' ?>" href="?modulo=inventario"><i class="bi bi-box-seam-fill me-1"></i> Inventario</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $moduloActual == 'garantias' ? 'active' : '' ?>" href="?modulo=garantias"><i class="bi bi-shield-check me-1"></i> Garantías</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $moduloActual == 'reportes' ? 'active' : '' ?>" href="?modulo=reportes"><i class="bi bi-bar-chart-fill me-1"></i> Reportes</a>
                </li>
                <li class="nav-item dropdown ms-lg-2">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="user-avatar"><i class="bi bi-person-fill"></i></span>
                        <span><?= htmlspecialchars($_SESSION['nombre_usuario'] ?? 'Usuario') ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                        <li><h6 class="dropdown-header"><i class="bi bi-shield-check me-1 text-success"></i>Sesión activa</h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger fw-semibold" href="controllers/AuthController.php?op=logout">
                                <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">