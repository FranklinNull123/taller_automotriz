<?php
require_once 'models/ClienteModel.php';
$clienteModel = new ClienteModel();
$clientes = $clienteModel->listarClientes();
$totalClientes = count($clientes);
$totalVehiculos = count(array_filter($clientes, function ($cliente) {
    return !empty($cliente['placa']);
}));
$mensaje = $_GET['msg'] ?? '';
?>

<style>
    .clients-page { max-width: 1180px; margin: 0 auto; }
    .client-hero {
        position: relative; overflow: hidden; min-height: 220px;
        background: linear-gradient(125deg, #101827 0%, #26364f 62%, #b91c31 150%);
        border-radius: 1.25rem; box-shadow: 0 16px 35px rgba(30, 41, 59, .18);
    }
    .client-hero:after {
        content: ""; position: absolute; width: 280px; height: 280px; right: -80px; top: -110px;
        border: 35px solid rgba(255,255,255,.07); border-radius: 50%;
    }
    .hero-content { position: relative; z-index: 1; }
    .hero-kicker { color: #fecdd3; letter-spacing: .12em; font-size: .7rem; font-weight: 800; text-transform: uppercase; }
    .hero-icon { color: rgba(255,255,255,.1); font-size: 8rem; line-height: 1; position: absolute; right: 12%; bottom: -1.5rem; }
    .client-stat { border: 0; border-radius: 1rem; transition: transform .2s ease, box-shadow .2s ease; box-shadow: 0 5px 16px rgba(30, 41, 59, .06); }
    .client-stat:hover { transform: translateY(-4px); box-shadow: 0 12px 25px rgba(30, 41, 59, .12); }
    .stat-icon { width: 52px; height: 52px; display: grid; place-items: center; border-radius: 16px; font-size: 1.35rem; }
    .client-list { border: 0; border-radius: 1.25rem; box-shadow: 0 8px 24px rgba(30, 41, 59, .07); }
    .client-list .card-header { border-bottom: 1px solid #edf0f4 !important; }
    .client-table thead th { white-space: nowrap; font-size: .72rem; text-transform: uppercase; letter-spacing: .05em; color: #64748b !important; background: #f8fafc; }
    .client-table tbody tr { transition: background-color .15s ease; border-color: #edf0f4; }
    .client-table tbody tr:hover { background-color: #fff7f7; }
    .client-table td { padding-top: 1rem; padding-bottom: 1rem; }
    .avatar-client { width: 38px; height: 38px; display: grid; place-items: center; border-radius: 12px; background: #fee2e2; color: #dc3545; font-weight: 800; }
    .plate-badge { background: #182334; color: #fff; border-radius: 6px; padding: .35rem .55rem; font-size: .72rem; letter-spacing: .06em; }
    .search-box { border: 1px solid #e2e8f0; border-radius: .7rem; }
    .search-box:focus-within { border-color: #dc3545; box-shadow: 0 0 0 .2rem rgba(220,53,69,.1); }
    .search-box input, .search-box .input-group-text { border: 0; background: transparent; }
    @media (max-width: 767px) { .hero-icon { display: none; } .client-hero { min-height: auto; } }
</style>

<?php if ($mensaje === 'exito'): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>Cliente y vehículo registrados correctamente.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php elseif ($mensaje === 'error'): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>No se pudo registrar. Verifica que el documento y la placa no estén repetidos.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php elseif ($mensaje === 'incompleto'): ?>
    <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="bi bi-info-circle-fill me-2"></i>Completa el nombre, documento y placa para continuar.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="clients-page">
<div class="client-hero text-white p-4 p-md-5 mb-4">
    <div class="hero-content row align-items-center g-4">
        <div class="col-lg-8">
            <div class="hero-kicker mb-3">Panel de administración</div>
            <h2 class="fw-bold display-6 mb-2"><i class="bi bi-people-fill me-2"></i>Clientes y vehículos</h2>
            <p class="text-white-50 mb-0">Toda la información de tus clientes, en un solo lugar.</p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <button type="button" class="btn btn-light text-dark px-4 py-2 shadow fw-bold" data-bs-toggle="modal" data-bs-target="#modalCliente">
                <i class="bi bi-plus-circle-fill text-danger me-2"></i>Nuevo registro
            </button>
        </div>
    </div>
    <i class="bi bi-car-front-fill hero-icon"></i>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card client-stat border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-danger-subtle text-danger"><i class="bi bi-person-vcard"></i></div>
                <div class="flex-grow-1"><small class="text-muted d-block">Clientes registrados</small><strong class="fs-2"><?= $totalClientes ?></strong></div>
                <i class="bi bi-arrow-up-right text-success"></i>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card client-stat border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary-subtle text-primary"><i class="bi bi-car-front"></i></div>
                <div class="flex-grow-1"><small class="text-muted d-block">Vehículos registrados</small><strong class="fs-2"><?= $totalVehiculos ?></strong></div>
                <i class="bi bi-arrow-up-right text-success"></i>
            </div>
        </div>
    </div>
</div>

<div class="card client-list overflow-hidden">
    <div class="card-header bg-white border-0 p-4">
        <div class="row align-items-center g-3">
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-danger fs-4"><i class="bi bi-list-ul"></i></span>
                    <div><h5 class="fw-bold mb-1">Directorio de clientes</h5><small class="text-muted">Consulta y gestiona tus registros.</small></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group search-box">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="search" id="buscarCliente" class="form-control" placeholder="Buscar cliente o placa..." aria-label="Buscar cliente">
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table client-table table-hover align-middle mb-0" id="tablaClientes">
            <thead class="table-light text-secondary">
                <tr>
                    <th class="ps-4 py-3">Cliente</th>
                    <th class="py-3">Documento</th>
                    <th class="py-3">Contacto</th>
                    <th class="py-3">Vehículo</th>
                    <th class="text-center pe-4 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($totalClientes > 0): foreach ($clientes as $c): ?>
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-2">
                                <span class="avatar-client"><?= strtoupper(substr($c['nombre'], 0, 1)) ?></span>
                                <div><div class="fw-bold text-dark"><?= htmlspecialchars($c['nombre']) ?></div><small class="text-muted">Cliente #<?= (int) $c['id'] ?></small></div>
                            </div>
                        </td>
                        <td><span class="badge bg-secondary-subtle text-secondary"><?= htmlspecialchars($c['documento']) ?></span></td>
                        <td>
                            <?php if (!empty($c['telefono'])): ?>
                                <a class="text-decoration-none text-dark" href="tel:<?= htmlspecialchars($c['telefono']) ?>"><i class="bi bi-telephone me-1 text-danger"></i><?= htmlspecialchars($c['telefono']) ?></a>
                            <?php else: ?><span class="text-muted">Sin teléfono</span><?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($c['placa'])): ?>
                                <span class="plate-badge"><i class="bi bi-car-front me-1"></i><?= htmlspecialchars($c['placa']) ?></span>
                                <small class="d-block text-muted mt-1"><?= htmlspecialchars(trim(($c['marca'] ?? '') . ' ' . ($c['modelo'] ?? ''))) ?></small>
                            <?php else: ?><span class="text-muted">Sin vehículo</span><?php endif; ?>
                        </td>
                        <td class="text-center pe-4">
                            <?php if (!empty($c['telefono'])): ?>
                                <a class="btn btn-sm btn-light border shadow-sm text-success" href="https://wa.me/51<?= htmlspecialchars($c['telefono']) ?>" target="_blank" rel="noopener" title="Contactar por WhatsApp"><i class="bi bi-whatsapp"></i></a>
                            <?php endif; ?>
                            <button type="button" class="btn btn-sm btn-light border shadow-sm text-primary" title="Editar próximamente" disabled><i class="bi bi-pencil-square"></i></button>
                        </td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="5" class="text-center py-5 text-muted"><i class="bi bi-people fs-1 d-block mb-2 text-light"></i>No hay clientes registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div id="sinResultados" class="text-center text-muted py-4 d-none">No se encontraron clientes con esa búsqueda.</div>
    </div>
</div>
</div>

<div class="modal fade" id="modalCliente" tabindex="-1" aria-labelledby="modalClienteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="modalClienteLabel"><i class="bi bi-person-plus me-2"></i>Registrar cliente y vehículo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form action="controllers/ClienteController.php?op=guardar" method="POST">
                <div class="modal-body p-4">
                    <h6 class="text-danger fw-bold mb-3">Datos del propietario</h6>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Nombre completo <span class="text-danger">*</span></label><input type="text" name="nombre" class="form-control" required maxlength="150"></div>
                        <div class="col-md-6"><label class="form-label">Documento <span class="text-danger">*</span></label><input type="text" name="documento" class="form-control" required maxlength="20"></div>
                        <div class="col-md-6"><label class="form-label">Teléfono</label><input type="tel" name="telefono" class="form-control" maxlength="20"></div>
                    </div>
                    <hr class="my-4">
                    <h6 class="text-danger fw-bold mb-3">Datos del vehículo</h6>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label">Placa <span class="text-danger">*</span></label><input type="text" name="placa" class="form-control text-uppercase" required maxlength="15"></div>
                        <div class="col-md-4"><label class="form-label">Marca</label><input type="text" name="marca" class="form-control" maxlength="50"></div>
                        <div class="col-md-4"><label class="form-label">Modelo</label><input type="text" name="modelo" class="form-control" maxlength="50"></div>
                        <div class="col-md-4"><label class="form-label">Año</label><input type="number" name="anio" class="form-control" min="1900" max="<?= date('Y') + 1 ?>"></div>
                    </div>
                    <small class="text-muted d-block mt-3"><span class="text-danger">*</span> Campos obligatorios</small>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-check-lg me-1"></i>Guardar registro</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const buscador = document.getElementById('buscarCliente');
    const filas = document.querySelectorAll('#tablaClientes tbody tr');
    const sinResultados = document.getElementById('sinResultados');

    buscador.addEventListener('input', function () {
        const termino = this.value.toLowerCase().trim();
        let visibles = 0;
        filas.forEach(function (fila) {
            const coincide = fila.textContent.toLowerCase().includes(termino);
            fila.classList.toggle('d-none', !coincide);
            if (coincide) visibles++;
        });
        sinResultados.classList.toggle('d-none', visibles > 0 || !termino);
    });
});
</script>
