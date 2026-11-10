<?php
require_once 'models/InventarioModel.php';
$invModel = new InventarioModel();
$repuestos = $invModel->listarRepuestos();
?>
<div class="row mb-4 align-items-center">
    <div class="col-md-8">
        <h2 class="fw-bold text-dark"><i class="bi bi-box-seam-fill text-danger me-2"></i>Repuestos e Inventario</h2>
        <p class="text-muted mb-0">Control de stock y refacciones disponibles.</p>
    </div>
    <div class="col-md-4 text-end">
        <button type="button" class="btn btn-danger shadow-sm fw-bold"><i class="bi bi-plus-circle me-2"></i>Agregar Repuesto</button>
    </div>
</div>
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
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
                        <td><span class="badge bg-success px-2 py-1"><?= $r['stock'] ?> unid.</span></td>
                        <td class="fw-bold text-success">S/ <?= number_format($r['precio_unitario'], 2) ?></td>
                        <td class="text-center pe-4">
                            <button class="btn btn-sm btn-light border shadow-sm text-danger"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">El inventario está vacío.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>