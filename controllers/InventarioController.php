<?php
require_once __DIR__ . '/../models/InventarioModel.php';
$inventarioModel = new InventarioModel();

$op = isset($_GET['op']) ? $_GET['op'] : '';

switch ($op) {
    case 'guardar':
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
        $id = isset($_GET['id']) ? $_GET['id'] : '';
        if (!empty($id)) {
            $respuesta = $inventarioModel->eliminarRepuesto($id);
            header("Location: ../index.php?modulo=inventario&msg=" . ($respuesta ? "eliminado" : "error"));
        }
        break;
}
?>