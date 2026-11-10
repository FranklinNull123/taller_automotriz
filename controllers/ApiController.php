<?php
require_once __DIR__ . '/../includes/auth.php';
requireAuth();
requireRole(['admin', 'recepcion']);

if (($_GET['op'] ?? '') !== 'vin') {
    http_response_code(404);
    exit('Recurso no encontrado.');
}

header('Content-Type: application/json; charset=utf-8');
$vin = strtoupper(trim($_GET['vin'] ?? ''));
if (!preg_match('/^[A-HJ-NPR-Z0-9]{17}$/', $vin)) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'message' => 'El VIN debe tener 17 caracteres válidos.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$url = 'https://vpic.nhtsa.dot.gov/api/vehicles/DecodeVinValuesExtended/' . rawurlencode($vin) . '?format=json';
$curl = curl_init($url);
curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 5,
    CURLOPT_TIMEOUT => 12,
    CURLOPT_HTTPHEADER => ['Accept: application/json'],
    CURLOPT_USERAGENT => 'TallerAutomotriz/1.0'
]);
$response = curl_exec($curl);
$curlError = curl_error($curl);
$status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

if ($response === false || $status < 200 || $status >= 300) {
    appLogger()->error('Error consultando NHTSA vPIC', ['status' => $status, 'error' => $curlError]);
    http_response_code(502);
    echo json_encode(['ok' => false, 'message' => 'No se pudo consultar el servicio de vehículos. Inténtalo nuevamente.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$payload = json_decode($response, true);
$vehicle = $payload['Results'][0] ?? null;
if (!is_array($vehicle)) {
    http_response_code(502);
    echo json_encode(['ok' => false, 'message' => 'El servicio devolvió una respuesta inválida.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$marca = trim((string) ($vehicle['Make'] ?? ''));
$modelo = trim((string) ($vehicle['Model'] ?? ''));
$anio = trim((string) ($vehicle['ModelYear'] ?? ''));
if ($marca === '' && $modelo === '' && $anio === '') {
    echo json_encode(['ok' => false, 'message' => 'No encontramos datos para ese VIN.'], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode([
    'ok' => true,
    'data' => ['marca' => $marca, 'modelo' => $modelo, 'anio' => $anio]
], JSON_UNESCAPED_UNICODE);
