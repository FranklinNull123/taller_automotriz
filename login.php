<?php
require_once __DIR__ . '/includes/auth.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso - Taller Automotriz</title>
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/css/tema.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background: #172033; min-height: 100vh; font-family: Inter, system-ui, sans-serif; }
        .login-art { background: linear-gradient(145deg, #111827, #334155); position: relative; overflow: hidden; }
        .login-art:after { content: ""; position: absolute; width: 420px; height: 420px; border: 55px solid rgba(230,57,70,.18); border-radius: 50%; right: -170px; bottom: -180px; }
        .login-card { 
            max-width: 400px; 
            width: 100%; 
            border-radius: 1rem; 
            box-shadow: 0 1rem 3rem rgba(0,0,0,0.5); 
        }
        .form-control:focus { box-shadow: none; border-color: #dc3545; }
    </style>
</head>
<body>

<div class="container-fluid p-0"><div class="row g-0 min-vh-100">
<div class="col-lg-6 d-none d-lg-flex login-art text-white align-items-center justify-content-center p-5">
    <div class="position-relative z-1 text-center">
        <i class="bi bi-car-front-fill text-danger" style="font-size: 6rem;"></i>
        <h1 class="fw-bold mt-4">Tu taller, bajo control.</h1>
        <p class="text-white-50 fs-5">Gestiona clientes, órdenes e inventario desde un solo lugar.</p>
    </div>
</div>
<div class="col-lg-6 d-flex align-items-center justify-content-center p-4">
<div class="card login-card border-0">
    <div class="card-body p-4 p-md-5">
        <div class="text-center mb-4">
            <i class="bi bi-car-front-fill text-danger mb-2" style="font-size: 3rem;"></i>
            <h4 class="fw-bold text-dark mt-2">TALLER AUTOMOTRIZ</h4>
            <p class="text-muted small">Ingresa tus credenciales para continuar</p>
        </div>
        
        <form action="controllers/AuthController.php?op=login" method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
            <?php if (($_GET['msg'] ?? '') === 'error'): ?>
                <div class="alert alert-danger small">Usuario o contraseña incorrectos.</div>
            <?php elseif (($_GET['msg'] ?? '') === 'locked'): ?>
                <div class="alert alert-warning small">Demasiados intentos. Espera unos minutos antes de volver a intentarlo.</div>
            <?php endif; ?>
            <div class="mb-3">
                <label class="form-label fw-bold text-secondary small">Usuario</label>
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-person text-muted"></i></span>
                    <input type="text" name="usuario" class="form-control border-start-0 ps-0" required placeholder="Ej: admin" autocomplete="off">
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold text-secondary small">Contraseña</label>
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-lock text-muted"></i></span>
                    <input type="password" name="password" class="form-control border-start-0 ps-0" required placeholder="••••••••">
                    <button class="btn btn-white border border-start-0" type="button" id="togglePassword" title="Mostrar contraseña"><i class="bi bi-eye"></i></button>
                </div>
            </div>
            <button type="submit" class="btn btn-danger w-100 fw-bold py-2 shadow">Iniciar Sesión</button>
        </form>
    </div>
    </div></div></div>
    <script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const input = document.querySelector('input[name="password"]');
        const visible = input.type === 'text';
        input.type = visible ? 'password' : 'text';
        this.innerHTML = visible ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
    });
    </script>

</body>
</html>