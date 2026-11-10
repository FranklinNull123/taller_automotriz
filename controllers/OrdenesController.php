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
        $repuesto_id = !empty($_POST['repuesto_id']) ? (int) $_POST['repuesto_id'] : null;
        $cantidad = !empty($_POST['cantidad']) ? (int) $_POST['cantidad'] : 0;
        
        if (!empty($vehiculo_id) && !empty($descripcion_falla)) {
            $respuesta = $ordenModel->registrarOrden($vehiculo_id, $descripcion_falla, $repuesto_id, $cantidad);
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

    case 'registrar_pago':
        requirePostRequest();
        requireValidCsrf();
        $id = trim($_POST['id'] ?? '');
        $monto = trim($_POST['monto'] ?? '');
        $metodo = trim($_POST['metodo'] ?? 'efectivo');
        $referencia = trim($_POST['referencia'] ?? '');
        $respuesta = !empty($id) ? $ordenModel->registrarPago($id, $monto, $metodo, $referencia) : false;
        header("Location: ../index.php?modulo=ordenes&msg=" . ($respuesta ? "pago_exito" : "pago_error"));
        break;
}
?>