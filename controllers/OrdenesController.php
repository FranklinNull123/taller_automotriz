<?php
require_once __DIR__ . '/../models/OrdenModel.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../vendor/autoload.php';
requireAuth();
requireRole(['admin', 'recepcion', 'mecanico']);
$ordenModel = new OrdenModel();

$op = isset($_GET['op']) ? $_GET['op'] : '';

function renderOrderPdf($orden) {
    $options = new Dompdf\Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', false);
    $dompdf = new Dompdf\Dompdf($options);
    $trackingUrl = rtrim((string) envValue('APP_URL', ''), '/') . '/index.php?modulo=ordenes&orden=' . (int) $orden['id'];
    $qrDataUri = (new Endroid\QrCode\QrCode($trackingUrl))->writeDataUri();
    $detalleHtml = '';
    foreach ($orden['detalles'] as $detalle) {
        $detalleHtml .= '<tr><td>' . htmlspecialchars($detalle['codigo'], ENT_QUOTES, 'UTF-8') . '</td><td>' . htmlspecialchars($detalle['descripcion'], ENT_QUOTES, 'UTF-8') . '</td><td>' . (int) $detalle['cantidad'] . '</td><td>S/ ' . number_format((float) $detalle['precio_unitario'], 2) . '</td></tr>';
    }
    $html = '<html><head><meta charset="UTF-8"><style>body{font-family:Helvetica;color:#243044;font-size:12px}.header{border-bottom:3px solid #b91c31;padding-bottom:12px}.grid{width:100%;margin:18px 0}.grid td{padding:4px 8px}.label{color:#64748b}table{width:100%;border-collapse:collapse;margin-top:18px}th,td{border:1px solid #dbe2ea;padding:8px}th{background:#f1f5f9;text-align:left}.total{text-align:right;font-size:15px;font-weight:bold}.qr{position:absolute;right:35px;top:25px;width:95px}</style></head><body><img class="qr" src="' . $qrDataUri . '"><div class="header"><h2>TALLER AUTOMOTRIZ</h2><div>Orden de servicio #' . (int) $orden['id'] . '</div></div><table class="grid"><tr><td class="label">Cliente</td><td>' . htmlspecialchars($orden['cliente'], ENT_QUOTES, 'UTF-8') . '</td><td class="label">Vehículo</td><td>' . htmlspecialchars(trim($orden['marca'] . ' ' . $orden['modelo'] . ' · ' . $orden['placa']), ENT_QUOTES, 'UTF-8') . '</td></tr><tr><td class="label">Estado</td><td>' . htmlspecialchars($orden['estado'], ENT_QUOTES, 'UTF-8') . '</td><td class="label">Ingreso</td><td>' . htmlspecialchars(date('d/m/Y', strtotime($orden['fecha_ingreso'])), ENT_QUOTES, 'UTF-8') . '</td></tr></table><h3>Falla reportada</h3><p>' . nl2br(htmlspecialchars($orden['descripcion_falla'], ENT_QUOTES, 'UTF-8')) . '</p><h3>Repuestos</h3><table><tr><th>Código</th><th>Descripción</th><th>Cantidad</th><th>Precio</th></tr>' . ($detalleHtml ?: '<tr><td colspan="4">Sin repuestos asignados</td></tr>') . '</table><p class="total">Total: S/ ' . number_format((float) $orden['total'], 2) . '<br>Pagado: S/ ' . number_format((float) $orden['total_pagado'], 2) . '<br>Saldo: S/ ' . number_format((float) $orden['saldo'], 2) . '</p><p>Escanea el código QR para consultar la orden en el sistema.</p></body></html>';
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    return $dompdf->output();
}

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

    case 'actualizar_cotizacion':
        requirePostRequest();
        requireValidCsrf();
        $id = trim($_POST['id'] ?? '');
        $estado = trim($_POST['cotizacion_estado'] ?? '');
        $respuesta = !empty($id) ? $ordenModel->actualizarCotizacion($id, $estado) : false;
        header("Location: ../index.php?modulo=ordenes&msg=" . ($respuesta ? "cotizacion_exito" : "cotizacion_error"));
        break;

    case 'pdf':
        $orden = $ordenModel->obtenerOrdenCompleta((int) ($_GET['id'] ?? 0));
        if (!$orden) {
            http_response_code(404);
            exit('Orden no encontrada.');
        }
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="orden-' . (int) $orden['id'] . '.pdf"');
        echo renderOrderPdf($orden);
        break;

    case 'enviar_email':
        requirePostRequest();
        requireValidCsrf();
        $orden = $ordenModel->obtenerOrdenCompleta((int) ($_POST['id'] ?? 0));
        $smtpHost = trim((string) envValue('MAIL_HOST', ''));
        if (!$orden || !filter_var($orden['email'], FILTER_VALIDATE_EMAIL) || $smtpHost === '') {
            header('Location: ../index.php?modulo=ordenes&msg=email_error');
            break;
        }
        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $smtpHost;
            $mail->Port = (int) envValue('MAIL_PORT', 587);
            $mail->SMTPAuth = trim((string) envValue('MAIL_USERNAME', '')) !== '';
            $mail->Username = envValue('MAIL_USERNAME', '');
            $mail->Password = envValue('MAIL_PASSWORD', '');
            if (strtolower((string) envValue('MAIL_ENCRYPTION', 'tls')) === 'ssl') {
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            }
            $mail->CharSet = 'UTF-8';
            $mail->setFrom(envValue('MAIL_FROM_ADDRESS', 'no-reply@example.com'), envValue('MAIL_FROM_NAME', 'Taller Automotriz'));
            $mail->addAddress($orden['email'], $orden['cliente']);
            $mail->Subject = 'Orden de servicio #' . (int) $orden['id'] . ' - Taller Automotriz';
            $mail->Body = 'Adjuntamos el comprobante de su orden de servicio.';
            $mail->addStringAttachment(renderOrderPdf($orden), 'orden-' . (int) $orden['id'] . '.pdf', 'base64', 'application/pdf');
            $mail->send();
            header('Location: ../index.php?modulo=ordenes&msg=email_exito');
        } catch (Throwable $exception) {
            appLogger()->error('Error enviando PDF por correo', ['error' => $exception->getMessage()]);
            header('Location: ../index.php?modulo=ordenes&msg=email_error');
        }
        break;
}
?>