<?php
require_once __DIR__ . '/includes/auth.php';
requireAuth(true);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar contraseña - Taller Automotriz</title>
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-dark d-flex align-items-center min-vh-100">
<main class="card border-0 shadow mx-auto" style="max-width: 460px; width: 92%;">
    <div class="card-body p-4 p-md-5">
        <div class="text-center mb-4">
            <i class="bi bi-shield-lock-fill text-danger fs-1"></i>
            <h2 class="fw-bold mt-2">Actualiza tu contraseña</h2>
            <p class="text-muted mb-0">Debes cambiar la contraseña inicial antes de continuar.</p>
        </div>
        <?php if (($_GET['msg'] ?? '') === 'error'): ?>
            <div class="alert alert-danger">La contraseña actual no es válida o las nuevas contraseñas no coinciden.</div>
        <?php endif; ?>
        <form action="controllers/AuthController.php?op=cambiar_password" method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
            <label class="form-label">Contraseña actual</label>
            <input type="password" name="password_actual" class="form-control mb-3" required>
            <label class="form-label">Nueva contraseña</label>
            <input type="password" name="password_nueva" class="form-control mb-3" minlength="8" required>
            <label class="form-label">Confirmar nueva contraseña</label>
            <input type="password" name="password_confirmacion" class="form-control mb-4" minlength="8" required>
            <button type="submit" class="btn btn-danger w-100 fw-bold">Guardar contraseña</button>
        </form>
    </div>
</main>
</body>
</html>
