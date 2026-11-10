<?php
require_once 'models/OrdenModel.php';
require_once 'models/ClienteModel.php';
require_once 'models/InventarioModel.php';
$ordenModel = new OrdenModel();
$listaOrdenes = $ordenModel->listarOrdenes();
$clientes = (new ClienteModel())->listarClientes();
$repuestos = (new InventarioModel())->listarRepuestos();
$estados = ['Pendiente', 'En Proceso', 'Terminado', 'Entregado'];
$mensaje = $_GET['msg'] ?? '';
?>
<?php if ($mensaje === 'exito'): ?><div class="alert alert-success border-0 shadow-sm"><i class="bi bi-check-circle me-2"></i>La orden se guardó correctamente.</div><?php elseif ($mensaje === 'pago_exito'): ?><div class="alert alert-success border-0 shadow-sm"><i class="bi bi-check-circle me-2"></i>Abono registrado correctamente.</div><?php elseif ($mensaje === 'error' || $mensaje === 'pago_error'): ?><div class="alert alert-danger border-0 shadow-sm"><i class="bi bi-exclamation-triangle me-2"></i>No se pudo completar la operación. Verifica el saldo y los datos.</div><?php endif; ?>
<div class="row mb-4 align-items-center">
    <div class="col-md-8">
        <h2 class="mb-0 fw-bold text-dark"><i class="bi bi-clipboard2-pulse text-danger me-2"></i>Gestión de Órdenes</h2>
        <p class="text-muted mb-0">Control de diagnósticos y estado de reparaciones.</p>
    </div>
    <div class="row g-3 mb-4">
        <?php foreach ($estados as $estado): $cantidad = count(array_filter($listaOrdenes, function ($orden) use ($estado) { return $orden['estado'] === $estado; })); ?>
            <div class="col-6 col-lg-3"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body"><small class="text-muted"><?= htmlspecialchars($estado) ?></small><div class="fs-3 fw-bold"><?= $cantidad ?></div></div></div></div>
        <?php endforeach; ?>
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
            <table class="table table-hover align-middle mb-0 table-interactive">
                <thead class="table-light text-secondary">
                    <tr>
                        <th class="ps-4 py-3 border-bottom-0">ID</th>
                        <th class="py-3 border-bottom-0">Vehículo (Placa)</th>
                        <th class="py-3 border-bottom-0">Falla Reportada</th>
                        <th class="py-3 border-bottom-0">Estado</th>
                        <th class="py-3 border-bottom-0">Fecha Ingreso</th>
                        <th class="py-3 border-bottom-0">Total</th>
                        <th class="py-3 border-bottom-0">Saldo</th>
                        <th class="text-center pe-4 py-3 border-bottom-0">Acciones</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php if(count($listaOrdenes) > 0): ?>
                        <?php foreach($listaOrdenes as $row): ?>
                            <?php $historialOrden = $ordenModel->listarHistorial((int) $row['id']); ?>
                            <tr>
                                <td class="ps-4 fw-bold text-secondary">#<?= (int) $row['id'] ?></td>
                                <td><span class="badge bg-dark border border-secondary"><i class="bi bi-upc"></i> <?= htmlspecialchars($row['placa']) ?></span></td>
                                <td class="text-truncate" style="max-width: 250px;"><?= htmlspecialchars($row['descripcion_falla']) ?></td>
                                <td>
                                    <?php 
                                        $badgeColor = 'bg-secondary';
                                        if($row['estado'] == 'Pendiente') $badgeColor = 'bg-warning text-dark';
                                        if($row['estado'] == 'En Proceso') $badgeColor = 'bg-info text-dark';
                                        if($row['estado'] == 'Terminado') $badgeColor = 'bg-success';
                                    ?>
                                    <span class="badge <?= htmlspecialchars($badgeColor, ENT_QUOTES, 'UTF-8') ?> px-3 py-2 rounded-pill"><?= htmlspecialchars($row['estado'], ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td><?= htmlspecialchars(date("d/m/Y", strtotime($row['fecha_ingreso'])), ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="fw-bold text-success">S/ <?= number_format((float) $row['total'], 2) ?></td>
                                <td class="fw-bold <?= (float) $row['saldo'] > 0 ? 'text-warning' : 'text-success' ?>">S/ <?= number_format((float) $row['saldo'], 2) ?></td>
                                <td class="text-center pe-4">
                                    <button type="button" class="btn btn-sm btn-light text-success shadow-sm border" title="Actualizar Estado" data-bs-toggle="modal" data-bs-target="#modalEstado<?= (int) $row['id'] ?>"><i class="bi bi-arrow-repeat"></i></button>
                                    <button type="button" class="btn btn-sm btn-light text-primary shadow-sm border" title="Registrar abono" data-bs-toggle="modal" data-bs-target="#modalPago<?= (int) $row['id'] ?>"><i class="bi bi-cash-coin"></i></button>
                                    <button class="btn btn-sm btn-light text-danger shadow-sm border" title="Imprimir PDF"><i class="bi bi-file-pdf-fill"></i></button>
                                </td>
                            </tr>
                            <div class="modal fade" id="modalEstado<?= (int) $row['id'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 shadow"><div class="modal-header bg-dark text-white"><h5 class="modal-title">Actualizar orden #<?= (int) $row['id'] ?></h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
                                    <form action="controllers/OrdenesController.php?op=actualizar_estado" method="POST"><div class="modal-body"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="id" value="<?= (int) $row['id'] ?>"><label class="form-label">Nuevo estado</label><select name="estado" class="form-select mb-4" required><?php foreach ($estados as $estado): ?><option <?= $row['estado'] === $estado ? 'selected' : '' ?>><?= htmlspecialchars($estado) ?></option><?php endforeach; ?></select><h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-1 text-danger"></i>Historial de la orden</h6><div class="border-start border-2 border-danger ps-3"><?php foreach ($historialOrden as $evento): ?><div class="mb-3"><div class="fw-semibold"><?= htmlspecialchars($evento['estado_nuevo']) ?></div><small class="text-muted"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($evento['creado_en']))) ?> · <?= htmlspecialchars($evento['usuario'] ?: 'Sistema') ?></small></div><?php endforeach; ?></div></div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-danger">Guardar estado</button></div></form>
                                </div></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3 text-light"></i>
                                <h6 class="fw-bold">No hay órdenes de servicio activas</h6>
                                <p class="small mb-0">Haz clic en "Nueva Orden" para registrar un vehículo en el taller.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="modal fade" id="modalPago<?= (int) $row['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 shadow">
                <div class="modal-header bg-dark text-white"><h5 class="modal-title"><i class="bi bi-cash-coin me-2"></i>Registrar abono · Orden #<?= (int) $row['id'] ?></h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
                <form action="controllers/OrdenesController.php?op=registrar_pago" method="POST">
                    <div class="modal-body p-4">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                        <div class="row g-3 mb-3"><div class="col-6"><small class="text-muted d-block">Total</small><strong>S/ <?= number_format((float) $row['total'], 2) ?></strong></div><div class="col-6"><small class="text-muted d-block">Saldo pendiente</small><strong class="text-warning">S/ <?= number_format((float) $row['saldo'], 2) ?></strong></div></div>
                        <label class="form-label">Monto del abono <span class="text-danger">*</span></label><input type="number" name="monto" class="form-control mb-3" min="0.01" max="<?= number_format((float) $row['saldo'], 2, '.', '') ?>" step="0.01" required>
                        <label class="form-label">Método de pago</label><select name="metodo" class="form-select mb-3"><option value="efectivo">Efectivo</option><option value="tarjeta">Tarjeta</option><option value="transferencia">Transferencia</option><option value="otro">Otro</option></select>
                        <label class="form-label">Referencia <small class="text-muted">(opcional)</small></label><input type="text" name="referencia" class="form-control" maxlength="100">
                    </div>
                    <div class="modal-footer bg-light"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-danger" <?= (float) $row['saldo'] <= 0 ? 'disabled' : '' ?>>Guardar abono</button></div>
                </form>
            </div></div>
        </div>

        <div class="modal fade" id="modalOrden" tabindex="-1" aria-labelledby="modalOrdenLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 shadow">
                <div class="modal-header bg-dark text-white"><h5 class="modal-title" id="modalOrdenLabel"><i class="bi bi-clipboard-plus me-2"></i>Nueva orden de servicio</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
                <form action="controllers/OrdenesController.php?op=guardar" method="POST">
                    <div class="modal-body p-4">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                        <label class="form-label">Vehículo <span class="text-danger">*</span></label>
                        <select name="vehiculo_id" class="form-select mb-3" required><option value="">Selecciona un vehículo</option><?php foreach ($clientes as $cliente): if (!empty($cliente['placa'])): ?><option value="<?= (int) $cliente['vehiculo_id'] ?>"><?= htmlspecialchars($cliente['placa'] . ' - ' . $cliente['nombre'] . ' (' . trim(($cliente['marca'] ?? '') . ' ' . ($cliente['modelo'] ?? '')) . ')') ?></option><?php endif; endforeach; ?></select>
                        <label class="form-label">Falla reportada <span class="text-danger">*</span></label>
                        <textarea name="descripcion_falla" class="form-control mb-3" rows="4" maxlength="500" required placeholder="Describe el problema del vehículo..."></textarea>
                        <div class="row g-3">
                            <div class="col-md-8"><label class="form-label">Repuesto a utilizar <small class="text-muted">(opcional)</small></label><select name="repuesto_id" class="form-select"><option value="">Sin repuesto asignado</option><?php foreach ($repuestos as $repuesto): ?><option value="<?= (int) $repuesto['id'] ?>"><?= htmlspecialchars($repuesto['codigo'] . ' - ' . $repuesto['descripcion']) ?></option><?php endforeach; ?></select></div>
                            <div class="col-md-4"><label class="form-label">Cantidad</label><input name="cantidad" type="number" class="form-control" min="1" value="1"></div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-danger"><i class="bi bi-check-lg me-1"></i>Crear orden</button></div>
                </form>
            </div></div>
        </div>
    </div>
</div>