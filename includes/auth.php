<?php
require_once __DIR__ . '/../config/bootstrap.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params([
        'httponly' => true,
        'secure' => filter_var(envValue('SESSION_SECURE', false), FILTER_VALIDATE_BOOLEAN),
        'samesite' => 'Lax'
    ]);
    session_start();
}

function requireAuth($allowPasswordChange = false) {
    if (empty($_SESSION['acceso'])) {
        $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $loginPath = strpos($script, '/controllers/') !== false ? '../login.php' : 'login.php';
        header('Location: ' . $loginPath);
        exit();
    }
    if (!$allowPasswordChange && !empty($_SESSION['debe_cambiar_password'])) {
        $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $changePath = strpos($script, '/controllers/') !== false ? '../cambiar-password.php' : 'cambiar-password.php';
        header('Location: ' . $changePath);
        exit();
    }
}

function requireRole($roles) {
    $roles = (array) $roles;
    if (empty($_SESSION['rol']) || !in_array($_SESSION['rol'], $roles, true)) {
        http_response_code(403);
        exit('No tienes permisos para realizar esta acción.');
    }
}

function securityHeaders() {
    if (headers_sent()) {
        return;
    }
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header("Content-Security-Policy: default-src 'self' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; img-src 'self' data:;");
}

function csrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function requireValidCsrf() {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        exit('Solicitud no válida.');
    }
}

function requirePostRequest() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        header('Allow: POST');
        exit('Método no permitido.');
    }

    securityHeaders();
}
