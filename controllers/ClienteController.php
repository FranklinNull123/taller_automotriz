<?php
require_once '../models/ClienteModel.php';
require_once __DIR__ . '/../includes/auth.php';
requireAuth();
requireRole(['admin', 'recepcion']);

$clienteModel = new ClienteModel();

// Capturar la operación a realizar desde la URL (ej. ?op=guardar)
$op = isset($_GET['op']) ? $_GET['op'] : '';

switch ($op) {
    case 'guardar':
        requirePostRequest();
        requireValidCsrf();
        // Recibir y limpiar los datos del formulario por POST
        $nombre = trim($_POST['nombre'] ?? '');
        $documento = trim($_POST['documento'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $email = trim($_POST['email'] ?? '');
        
        $placa = strtoupper(trim($_POST['placa'] ?? ''));
        $marca = trim($_POST['marca'] ?? '');
        $modelo = trim($_POST['modelo'] ?? '');
        $anio = trim($_POST['anio'] ?? '');

        // Validar que los campos obligatorios no estén vacíos
        if (!empty($nombre) && !empty($documento) && !empty($placa)) {
            
            // Llamar al modelo para registrar en ambas tablas
            $respuesta = $clienteModel->registrarClienteYVehiculo($nombre, $documento, $telefono, $email, $placa, $marca, $modelo, $anio);

            if ($respuesta) {
                // Redirigir con mensaje de éxito
                header("Location: ../index.php?modulo=clientes&msg=exito");
            } else {
                // Redirigir con mensaje de error (ej. placa duplicada)
                header("Location: ../index.php?modulo=clientes&msg=error");
            }
        } else {
            header("Location: ../index.php?modulo=clientes&msg=incompleto");
        }
        exit();
        break;

    // Aquí agregaremos futuros casos como 'editar', 'eliminar', 'listar'
}
?>