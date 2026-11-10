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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { 
            background-color: #1e293b; 
            height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-family: system-ui, -apple-system, sans-serif;
        }
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

<div class="card login-card border-0">
    <div class="card-body p-5">
        <div class="text-center mb-4">
            <i class="bi bi-car-front-fill text-danger mb-2" style="font-size: 3rem;"></i>
            <h4 class="fw-bold text-dark mt-2">TALLER AUTOMOTRIZ</h4>
            <p class="text-muted small">Ingresa tus credenciales para continuar</p>
        </div>
        
        <form action="controllers/AuthController.php?op=login" method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
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
                </div>
            </div>
            <button type="submit" class="btn btn-danger w-100 fw-bold py-2 shadow">Iniciar Sesión</button>
        </form>
    </div>
</div>

</body>
</html>