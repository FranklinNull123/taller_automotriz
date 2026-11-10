<?php
require_once 'models/OrdenModel.php';
$ordenModel = new OrdenModel();
$listaOrdenes = $ordenModel->listarOrdenes();
?>
<div class="row mb-4 align-items-center">
    <div class="col-md-8">
        <h2 class="mb-0 fw-bold text-dark"><i class="bi bi-clipboard2-pulse text-danger me-2"></i>Gestión de Órdenes</h2>
        <p class="text-muted mb-0">Control de diagnósticos y estado de reparaciones.</p>
    </div>
    <div class="col-md-4 text-end">
        <!-- El botón ya tiene el activador para el modal que crearemos -->
        <button type="button" class="btn btn-danger px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalOrden">
            <i class="bi bi-plus-circle me-2"></i>Nueva Orden
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary">
                    <tr>
                        <th class="ps-4 py-3 border-bottom-0">ID</th>
                        <th class="py-3 border-bottom-0">Vehículo (Placa)</th>
                        <th class="py-3 border-bottom-0">Falla Reportada</th>
                        <th class="py-3 border-bottom-0">Estado</th>
                        <th class="py-3 border-bottom-0">Fecha Ingreso</th>
                        <th class="text-center pe-4 py-3 border-bottom-0">Acciones</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php if(count($listaOrdenes) > 0): ?>
                        <?php foreach($listaOrdenes as $row): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-secondary">#<?= $row['id'] ?></td>
                                <td><span class="badge bg-dark border border-secondary"><i class="bi bi-upc"></i> <?= htmlspecialchars($row['placa']) ?></span></td>
                                <td class="text-truncate" style="max-width: 250px;"><?= htmlspecialchars($row['descripcion_falla']) ?></td>
                                <td>
                                    <?php 
                                        $badgeColor = 'bg-secondary';
                                        if($row['estado'] == 'Pendiente') $badgeColor = 'bg-warning text-dark';
                                        if($row['estado'] == 'En Proceso') $badgeColor = 'bg-info text-dark';
                                        if($row['estado'] == 'Terminado') $badgeColor = 'bg-success';
                                    ?>
                                    <span class="badge <?= $badgeColor ?> px-3 py-2 rounded-pill"><?= $row['estado'] ?></span>
                                </td>
                                <td><?= date("d/m/Y", strtotime($row['fecha_ingreso'])) ?></td>
                                <td class="text-center pe-4">
                                    <button class="btn btn-sm btn-light text-success shadow-sm border" title="Actualizar Estado"><i class="bi bi-arrow-repeat"></i></button>
                                    <button class="btn btn-sm btn-light text-danger shadow-sm border" title="Imprimir PDF"><i class="bi bi-file-pdf-fill"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3 text-light"></i>
                                <h6 class="fw-bold">No hay órdenes de servicio activas</h6>
                                <p class="small mb-0">Haz clic en "Nueva Orden" para registrar un vehículo en el taller.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>