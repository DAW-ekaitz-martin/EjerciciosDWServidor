<?php
declare(strict_types=1);
require __DIR__ . '/_comun.php';
metodo_permitido(['GET']);
$tipo = $_GET['tipo'] ?? 'http';
if (!is_string($tipo)) {
    error_respuesta('tipo debe ser texto.', 422);
}
if ($tipo === 'http') {
    error_respuesta('Fallo HTTP simulado para la práctica.', 500);
}
if ($tipo === 'json') {
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo '{"ok":true,"datos":'; // Roto a propósito.
    exit;
}
if ($tipo === 'app') {
    json_respuesta(['ok' => false, 'error' => ['mensaje' => 'Operación rechazada con HTTP 200 para comparar errores.']]);
}
error_respuesta('Usa tipo=http, json o app.', 422);
