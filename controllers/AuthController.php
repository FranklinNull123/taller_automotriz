<?php
require_once __DIR__ . '/../includes/auth.php';

$op = isset($_GET['op']) ? $_GET['op'] : '';

switch ($op) {
    case 'login':
        requirePostRequest();
        requireValidCsrf();
        $usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';
        $now = time();
        $lockoutSeconds = (int) envValue('LOGIN_LOCKOUT_SECONDS', 900);
        $maxAttempts = (int) envValue('LOGIN_MAX_ATTEMPTS', 5);
        if (!empty($_SESSION['login_locked_until']) && $_SESSION['login_locked_until'] > $now) {
            header("Location: ../login.php?msg=locked");
            exit();
        }

        require_once __DIR__ . '/../config/conexion.php';
        $conexion = new Conexion();
        $conn = $conexion->conectar();
        $stmt = $conn->prepare(
            "SELECT id, usuario, password_hash, rol, debe_cambiar_password
             FROM usuarios
             WHERE usuario = :usuario AND activo = 1
             LIMIT 1"
        );
        $stmt->execute([':usuario' => $usuario]);
        $usuarioEncontrado = $stmt->fetch();

        if ($usuarioEncontrado && password_verify($password, $usuarioEncontrado['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['acceso'] = true;
            $_SESSION['usuario_id'] = (int) $usuarioEncontrado['id'];
            $_SESSION['nombre_usuario'] = $usuarioEncontrado['usuario'];
            $_SESSION['rol'] = $usuarioEncontrado['rol'];
            $_SESSION['debe_cambiar_password'] = (bool) $usuarioEncontrado['debe_cambiar_password'];
            $_SESSION['login_attempts'] = 0;
            unset($_SESSION['login_locked_until']);
            header("Location: ../index.php");
        } else {
            appLogger()->warning('Intento de login fallido', [
                'usuario' => $usuario,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'desconocida'
            ]);
            $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
            if ($_SESSION['login_attempts'] >= $maxAttempts) {
                $_SESSION['login_locked_until'] = $now + $lockoutSeconds;
                $_SESSION['login_attempts'] = 0;
            }
            header("Location: ../login.php?msg=error");
            exit();
        }
        break;

    case 'logout':
        requirePostRequest();
        requireValidCsrf();
        $_SESSION = [];
        session_destroy();
        header("Location: ../login.php");
        exit();
        break;

    case 'cambiar_password':
        requireAuth(true);
        requirePostRequest();
        requireValidCsrf();
        $actual = $_POST['password_actual'] ?? '';
        $nueva = $_POST['password_nueva'] ?? '';
        $confirmacion = $_POST['password_confirmacion'] ?? '';
        if (strlen($nueva) < 8 || $nueva !== $confirmacion) {
            header("Location: ../cambiar-password.php?msg=error");
            exit();
        }

        require_once __DIR__ . '/../config/conexion.php';
        $conn = (new Conexion())->conectar();
        $stmt = $conn->prepare("SELECT password_hash FROM usuarios WHERE id = :id AND activo = 1");
        $stmt->execute([':id' => (int) $_SESSION['usuario_id']]);
        $hash = $stmt->fetchColumn();
        if (!$hash || !password_verify($actual, $hash)) {
            header("Location: ../cambiar-password.php?msg=error");
            exit();
        }

        $stmt = $conn->prepare(
            "UPDATE usuarios
             SET password_hash = :password_hash, debe_cambiar_password = 0
             WHERE id = :id"
        );
        $stmt->execute([
            ':password_hash' => password_hash($nueva, PASSWORD_ARGON2ID),
            ':id' => (int) $_SESSION['usuario_id']
        ]);
        $_SESSION['debe_cambiar_password'] = false;
        header("Location: ../index.php");
        exit();
}
?>