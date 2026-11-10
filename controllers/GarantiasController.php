<?php
require_once __DIR__ . '/../models/GarantiaModel.php';
require_once __DIR__ . '/../includes/auth.php';
requireAuth();
requireRole(['admin', 'recepcion']);
$garantiaModel = new GarantiaModel();

$op = isset($_GET['op']) ? $_GET['op'] : '';

switch ($op) {
    case 'guardar':
        requirePostRequest();
        requireValidCsrf();
        $orden_id = trim($_POST['orden_id']);
        $servicio_realizado = trim($_POST['servicio_realizado']);
        $fecha_vencimiento = trim($_POST['fecha_vencimiento']);

        if (!empty($orden_id) && !empty($servicio_realizado) && !empty($fecha_vencimiento)) {
            $respuesta = $garantiaModel->registrarGarantia($orden_id, $servicio_realizado, $fecha_vencimiento);
            header("Location: ../index.php?modulo=garantias&msg=" . ($respuesta ? "exito" : "error"));
        } else {
            header("Location: ../index.php?modulo=garantias&msg=incompleto");
        }
        break;
}
?>