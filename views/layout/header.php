<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Taller Automotriz</title>
    <!-- Bootstrap CSS Local -->
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/css/tema.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/datatables.net-dt@2.1.8/css/dataTables.dataTables.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { font-family: Inter, system-ui, -apple-system, sans-serif; }
        .user-avatar { width: 30px; height: 30px; display: inline-grid; place-items: center; border-radius: 50%; background: #dc3545; color: #fff; font-size: .85rem; }
        .dropdown-menu { border-radius: .75rem; min-width: 190px; }
        .dropdown-item { border-radius: .45rem; margin: .15rem .35rem; width: auto; }
        .dropdown-item:hover { background: #fff1f2; color: #be123c !important; }
        
        /* Clases para las tarjetas del Dashboard principal */
        .card-dashboard { transition: transform 0.2s, box-shadow 0.2s; border-radius: 12px; }
        .card-dashboard:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; border-color: #cbd5e1 !important; }
        .icon-lg { font-size: 3rem; }
        .dashboard-hero { background: linear-gradient(120deg, #172033, #334155); border-radius: 1.25rem; }
        .kpi-card { border: 0; border-radius: 1rem; transition: transform .2s ease, box-shadow .2s ease; }
        .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 .75rem 1.5rem rgba(15, 23, 42, .12) !important; }
        .kpi-icon { width: 44px; height: 44px; display: grid; place-items: center; border-radius: 12px; font-size: 1.25rem; }
    </style>
</head>
<body>

<?php 
// Detectar el módulo actual para iluminar el menú
$moduloActual = isset($_GET['modulo']) ? $_GET['modulo'] : ''; 
?>

<div class="app-shell">
<aside class="app-sidebar">
    <a class="brand-lockup d-flex align-items-center gap-2 text-decoration-none" href="index.php">
        <span class="brand-mark"><i class="bi bi-car-front-fill"></i></span>
        <span class="brand-text fw-bold text-white">TALLER <span class="text-danger">AUTOMOTRIZ</span></span>
    </a>
    <div class="sidebar-label">Menú principal</div>
    <nav>
        <a class="sidebar-link <?= $moduloActual == '' ? 'active' : '' ?>" href="index.php"><i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span></a>
        <a class="sidebar-link <?= $moduloActual == 'clientes' ? 'active' : '' ?>" href="?modulo=clientes"><i class="bi bi-people-fill"></i><span>Clientes</span></a>
        <a class="sidebar-link <?= $moduloActual == 'ordenes' ? 'active' : '' ?>" href="?modulo=ordenes"><i class="bi bi-tools"></i><span>Órdenes</span></a>
        <a class="sidebar-link <?= $moduloActual == 'inventario' ? 'active' : '' ?>" href="?modulo=inventario"><i class="bi bi-box-seam-fill"></i><span>Inventario</span></a>
        <a class="sidebar-link <?= $moduloActual == 'garantias' ? 'active' : '' ?>" href="?modulo=garantias"><i class="bi bi-shield-check"></i><span>Garantías</span></a>
        <a class="sidebar-link <?= $moduloActual == 'reportes' ? 'active' : '' ?>" href="?modulo=reportes"><i class="bi bi-bar-chart-fill"></i><span>Reportes</span></a>
    </nav>
</aside>
<main class="app-main">
<header class="app-topbar d-flex align-items-center justify-content-between px-3 px-lg-4">
    <div class="d-flex align-items-center gap-3">
        <button class="sidebar-toggle" type="button" title="Contraer menú"><i class="bi bi-layout-sidebar-inset"></i></button>
        <div class="input-group topbar-search">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="search" class="form-control" placeholder="Buscar en el taller..." aria-label="Buscar">
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button id="themeToggle" class="theme-toggle" type="button" title="Cambiar tema"><i class="bi bi-moon-stars"></i></button>
        <div class="dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="user-avatar"><i class="bi bi-person-fill"></i></span>
                <span class="d-none d-md-inline"><?= htmlspecialchars($_SESSION['nombre_usuario'] ?? 'Usuario') ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                <li><h6 class="dropdown-header"><i class="bi bi-shield-check me-1 text-success"></i>Sesión activa</h6></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="controllers/AuthController.php?op=logout" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="dropdown-item text-danger fw-semibold"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
<div class="app-content">