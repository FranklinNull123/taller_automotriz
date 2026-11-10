<?php
require_once 'models/InventarioModel.php';
$invModel = new InventarioModel();
$repuestos = $invModel->listarRepuestos();
$stockBajo = count(array_filter($repuestos, function ($repuesto) {
    return (int) $repuesto['stock'] <= 5;
}));
$valorInventario = array_reduce($repuestos, function ($total, $repuesto) {
    return $total + ((float) $repuesto['stock'] * (float) $repuesto['precio_unitario']);
}, 0);
$mensaje = $_GET['msg'] ?? '';
?>
<?php if ($mensaje === 'exito' || $mensaje === 'eliminado'): ?>
    <div class="alert alert-success border-0 shadow-sm"><i class="bi bi-check-circle me-2"></i><?= $mensaje === 'eliminado' ? 'Repuesto eliminado correctamente.' : 'Repuesto registrado correctamente.' ?></div>
<?php elseif ($mensaje === 'error'): ?>
    <div class="alert alert-danger border-0 shadow-sm"><i class="bi bi-exclamation-triangle me-2"></i>No se pudo completar la operación. Verifica los datos.</div>
<?php endif; ?>

<div class="row mb-4 align-items-center">
    <div class="col-md-8">
        <h2 class="fw-bold text-dark"><i class="bi bi-box-seam-fill text-danger me-2"></i>Repuestos e Inventario</h2>
        <p class="text-muted mb-0">Control de stock y refacciones disponibles.</p>
    </div>
    <div class="col-md-4 text-end">
        <button type="button" class="btn btn-danger shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalRepuesto"><i class="bi bi-plus-circle me-2"></i>Agregar Repuesto</button>
    </div>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body d-flex align-items-center gap-3"><span class="kpi-icon bg-danger-subtle text-danger"><i class="bi bi-box-seam"></i></span><div><small class="text-muted">Artículos registrados</small><div class="fs-4 fw-bold"><?= count($repuestos) ?></div></div></div></div></div>
    <div class="col-md-4"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body d-flex align-items-center gap-3"><span class="kpi-icon bg-warning-subtle text-warning"><i class="bi bi-exclamation-triangle"></i></span><div><small class="text-muted">Stock bajo</small><div class="fs-4 fw-bold"><?= $stockBajo ?></div></div></div></div></div>
    <div class="col-md-4"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body d-flex align-items-center gap-3"><span class="kpi-icon bg-success-subtle text-success"><i class="bi bi-cash-stack"></i></span><div><small class="text-muted">Valor del inventario</small><div class="fs-4 fw-bold">S/ <?= number_format($valorInventario, 2) ?></div></div></div></div></div>
</div>
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-interactive">
                <thead class="table-light text-secondary">
                    <tr>
                        <th class="ps-4 py-3">Código</th>
                        <th class="py-3">Descripción</th>
                        <th class="py-3">Categoría</th>
                        <th class="py-3">Stock</th>
                        <th class="py-3">Precio Unit.</th>
                        <th class="text-center pe-4 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($repuestos) > 0): foreach($repuestos as $r): ?>
                    <tr>
                        <td class="ps-4"><span class="badge bg-dark"><?= htmlspecialchars($r['codigo']) ?></span></td>
                        <td class="fw-bold"><?= htmlspecialchars($r['descripcion']) ?></td>
                        <td><?= htmlspecialchars($r['categoria']) ?></td>
                        <td><span class="badge <?= (int) $r['stock'] <= 5 ? 'bg-warning text-dark' : 'bg-success' ?> px-2 py-1"><?= (int) $r['stock'] ?> unid.</span></td>
                        <td class="fw-bold text-success">S/ <?= number_format((float) $r['precio_unitario'], 2) ?></td>
                        <td class="text-center pe-4">
                            <form action="controllers/InventarioController.php?op=eliminar" method="POST" class="d-inline" data-confirm="El repuesto será eliminado del inventario.">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-light border shadow-sm text-danger" title="Eliminar repuesto"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">El inventario está vacío.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modalRepuesto" tabindex="-1" aria-labelledby="modalRepuestoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalRepuestoLabel"><i class="bi bi-box-seam me-2"></i>Nuevo repuesto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="controllers/InventarioController.php?op=guardar" method="POST">
                    <div class="modal-body p-4">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">Código <span class="text-danger">*</span></label><input name="codigo" class="form-control" maxlength="30" required></div>
                            <div class="col-md-6"><label class="form-label">Categoría</label><input name="categoria" class="form-control" maxlength="80"></div>
                            <div class="col-12"><label class="form-label">Descripción <span class="text-danger">*</span></label><input name="descripcion" class="form-control" maxlength="150" required></div>
                            <div class="col-md-6"><label class="form-label">Stock inicial</label><input name="stock" type="number" class="form-control" min="0" value="0"></div>
                            <div class="col-md-6"><label class="form-label">Precio unitario <span class="text-danger">*</span></label><input name="precio_unitario" type="number" class="form-control" min="0" step="0.01" required></div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-danger" type="submit"><i class="bi bi-check-lg me-1"></i>Guardar repuesto</button></div>
                </form>
            </div>
        </div>
    </div>
</div>