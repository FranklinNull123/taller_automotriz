<?php
require_once 'models/GarantiaModel.php';
$garModel = new GarantiaModel();
$garantias = $garModel->listarGarantias();
?>
<div class="row mb-4 align-items-center">
    <div class="col-md-8">
        <h2 class="fw-bold text-dark"><i class="bi bi-shield-check text-danger me-2"></i>Garantías y Recordatorios</h2>
        <p class="text-muted mb-0">Seguimiento de coberturas activas por reparaciones.</p>
    </div>
</div>
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary">
                    <tr>
                        <th class="ps-4 py-3">ID Orden</th>
                        <th class="py-3">Cliente</th>
                        <th class="py-3">Servicio Realizado</th>
                        <th class="py-3">Vencimiento</th>
                        <th class="py-3">Estado</th>
                        <th class="text-center pe-4 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($garantias) > 0): foreach ($garantias as $g): ?>
                            <tr>
                                <td class="ps-4 fw-bold">#<?= $g['orden_id'] ?></td>
                                <td><?= htmlspecialchars($g['cliente']) ?></td>
                                <td><?= htmlspecialchars($g['servicio_realizado']) ?></td>
                                <td><?= date("d/m/Y", strtotime($g['fecha_vencimiento'])) ?></td>
                                <td><span class="badge bg-success px-3 py-2 rounded-pill"><?= $g['estado'] ?></span></td>
                                <td class="text-center pe-4">
                                    <a href="https://wa.me/51<?= $g['telefono'] ?>?text=Hola%20<?= urlencode($g['cliente']) ?>,%20le%20escribimos%20de%20Taller%20Automotriz%20para%20recordarle%20sobre%20la%20garantía%20de%20su%20servicio%20(Vence:%20<?= $g['fecha_vencimiento'] ?>)."
                                        target="_blank"
                                        class="btn btn-sm btn-light border shadow-sm text-success"
                                        title="Enviar WhatsApp">
                                        <i class="bi bi-whatsapp"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach;
                    else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No hay garantías activas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>