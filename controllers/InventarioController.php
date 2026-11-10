<?php
require_once __DIR__ . '/../models/InventarioModel.php';
require_once __DIR__ . '/../includes/auth.php';
requireAuth();
$inventarioModel = new InventarioModel();

$op = isset($_GET['op']) ? $_GET['op'] : '';

switch ($op) {
    case 'guardar':
        requirePostRequest();
        requireValidCsrf();
        $codigo = trim($_POST['codigo']);
        $descripcion = trim($_POST['descripcion']);
        $categoria = trim($_POST['categoria']);
        $stock = trim($_POST['stock']);
        $precio_unitario = trim($_POST['precio_unitario']);

        if (!empty($codigo) && !empty($descripcion) && !empty($precio_unitario)) {
            $respuesta = $inventarioModel->registrarRepuesto($codigo, $descripcion, $categoria, $stock, $precio_unitario);
            header("Location: ../index.php?modulo=inventario&msg=" . ($respuesta ? "exito" : "error"));
        } else {
            header("Location: ../index.php?modulo=inventario&msg=incompleto");
        }
        break;

    case 'eliminar':
        requirePostRequest();
        requireValidCsrf();
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        if (!empty($id)) {
            $respuesta = $inventarioModel->eliminarRepuesto($id);
            header("Location: ../index.php?modulo=inventario&msg=" . ($respuesta ? "eliminado" : "error"));
        }
        break;
}
?>