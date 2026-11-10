<?php
require_once __DIR__ . '/../includes/auth.php';

$op = isset($_GET['op']) ? $_GET['op'] : '';

switch ($op) {
    case 'login':
        requirePostRequest();
        requireValidCsrf();
        $usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';

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
            header("Location: ../index.php");
        } else {
            // Retorna al login con una alerta de error en la URL
            header("Location: ../login.php?msg=error");
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
}
?>