<?php
declare(strict_types=1);

// Este archivo contiene funciones, no produce salida al incluirlo.
function json_respuesta(array $cuerpo, int $estado = 200): void
{
    http_response_code($estado);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($cuerpo, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    exit;
}

function error_respuesta(string $mensaje, int $estado, array $campos = []): void
{
    $error = ['mensaje' => $mensaje];
    if ($campos !== []) {
        $error['campos'] = $campos;
    }
    json_respuesta(['ok' => false, 'error' => $error], $estado);
}

function metodo_permitido(array $metodos): string
{
    $metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    if (!in_array($metodo, $metodos, true)) {
        header('Allow: ' . implode(', ', $metodos));
        error_respuesta('Método no permitido.', 405);
    }
    return $metodo;
}

function tipo_contenido(): string
{
    return strtolower(trim(explode(';', $_SERVER['CONTENT_TYPE'] ?? '')[0]));
}

function largo(string $texto): int
{
    return function_exists('mb_strlen') ? mb_strlen($texto, 'UTF-8') : strlen($texto);
}

function validar_contacto(array $entrada): array
{
    $campos = [];
    $nombre = $entrada['nombre'] ?? null;
    $email = $entrada['email'] ?? null;
    if (!is_string($nombre) || largo(trim($nombre)) < 2 || largo(trim($nombre)) > 80) {
        $campos['nombre'] = 'Escribe un nombre de 2 a 80 caracteres.';
    }
    if (!is_string($email) || strlen(trim($email)) > 120 || !filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
        $campos['email'] = 'Escribe un email válido de hasta 120 caracteres.';
    }
    if ($campos !== []) {
        error_respuesta('Revisa los campos.', 422, $campos);
    }
    return ['nombre' => trim($nombre), 'email' => trim($email)];
}
