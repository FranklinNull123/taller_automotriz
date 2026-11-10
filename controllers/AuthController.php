<?php
session_start();

$op = isset($_GET['op']) ? $_GET['op'] : '';

switch ($op) {
    case 'login':
        $usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';

        // Datos ficticios de acceso
        $usuario_valido = 'admin';
        $password_valida = '123456';

        if ($usuario === $usuario_valido && $password === $password_valida) {
            $_SESSION['acceso'] = true;
            $_SESSION['nombre_usuario'] = 'Administrador';
            header("Location: ../index.php");
        } else {
            // Retorna al login con una alerta de error en la URL
            header("Location: ../login.php?msg=error");
        }
        break;

    case 'logout':
        $_SESSION = [];
        session_destroy();
        header("Location: ../login.php");
        exit();
        break;
}
?>