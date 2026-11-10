<?php
require_once __DIR__ . '/../models/OrdenModel.php';
require_once __DIR__ . '/../includes/auth.php';
requireAuth();
requireRole(['admin', 'recepcion', 'mecanico']);
$ordenModel = new OrdenModel();

$op = isset($_GET['op']) ? $_GET['op'] : '';

switch ($op) {
    case 'guardar':
        requirePostRequest();
        requireValidCsrf();
        $vehiculo_id = trim($_POST['vehiculo_id']);
        $descripcion_falla = trim($_POST['descripcion_falla']);
        
        if (!empty($vehiculo_id) && !empty($descripcion_falla)) {
            // El método registrarOrden se creará en el modelo después
            $respuesta = $ordenModel->registrarOrden($vehiculo_id, $descripcion_falla);
            header("Location: ../index.php?modulo=ordenes&msg=" . ($respuesta ? "exito" : "error"));
        } else {
            header("Location: ../index.php?modulo=ordenes&msg=incompleto");
        }
        break;

    case 'actualizar_estado':
        requirePostRequest();
        requireValidCsrf();
        $id = trim($_POST['id']);
        $estado = trim($_POST['estado']);
        
        if (!empty($id) && !empty($estado)) {
            $respuesta = $ordenModel->actualizarEstado($id, $estado);
            header("Location: ../index.php?modulo=ordenes&msg=" . ($respuesta ? "exito" : "error"));
        }
        break;
}
?>