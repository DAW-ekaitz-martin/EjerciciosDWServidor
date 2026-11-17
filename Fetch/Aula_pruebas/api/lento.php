<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    header('Allow: GET');
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => ['mensaje' => 'Método no permitido.']], JSON_UNESCAPED_UNICODE);
    exit;
}

$valor = $_GET['ms'] ?? '1000';
if (!is_string($valor) || filter_var($valor, FILTER_VALIDATE_INT) === false) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => ['mensaje' => 'ms debe ser un entero.']], JSON_UNESCAPED_UNICODE);
    exit;
}

$ms = max(0, min(2000, (int) $valor));
usleep($ms * 1000);
echo json_encode([
    'ok' => true,
    'datos' => ['espera_ms' => $ms, 'mensaje' => 'Respuesta recibida desde PHP.'],
], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
