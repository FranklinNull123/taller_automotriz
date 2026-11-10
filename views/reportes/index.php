<?php
require_once 'models/ReporteModel.php';
$reporteModel = new ReporteModel();
$estadisticas = $reporteModel->obtenerEstadisticas();
?>
<div class="row mb-4 align-items-center">
    <div class="col-md-8">
        <h2 class="mb-0">Reportes de Productividad</h2>
    </div>
    <div class="col-md-4 text-end">
        <button type="button" class="btn btn-secondary">
            <i class="bi bi-file-earmark-pdf-fill me-2"></i>Exportar PDF
        </button>
    </div>
</div>
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body p-4 text-center">
                <h3 class="fw-bold">0</h3>
                <p class="mb-0">Órdenes Atendidas (Mes)</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-warning text-dark">
            <div class="card-body p-4 text-center">
                <h3 class="fw-bold">0</h3>
                <p class="mb-0">Vehículos en Taller</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body p-4 text-center">
                <h3 class="fw-bold">S/ 0.00</h3>
                <p class="mb-0">Ingresos Totales</p>
            </div>
        </div>
    </div>
</div>