<?php
// Datos y funciones comunes. Se incluye con require_once en todas las páginas.

$precios = [
    'basico' => 6.50,
    'completo' => 9.00,
    'vegetariano' => 8.00
];

$nombresMenu = [
    'basico' => 'Básico',
    'completo' => 'Completo',
    'vegetariano' => 'Vegetariano'
];

// Escapar texto para HTML (contenido y atributos)
function e($texto)
{
    return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
}

// Formato solo para mostrar: 27.8 -> "27,80 €"
function euros($importe)
{
    return number_format($importe, 2, ',', '.') . ' €';
}

// C) Devuelve NÚMEROS, no cadenas formateadas
function calcularPedido($precioUnitario, $unidades, $esSocio, $entrega)
{
    $subtotal = $precioUnitario * $unidades;
    $descuento = $esSocio ? $subtotal * 0.10 : 0;
    $trasDescuento = $subtotal - $descuento;

    $envio = 0;
    if ($entrega === 'envio' && $trasDescuento < 25) {
        $envio = 3.50;
    }

    return [
        'subtotal' => $subtotal,
        'descuento' => $descuento,
        'envio' => $envio,
        'total' => $trasDescuento + $envio
    ];
}

// E) Tema: solo valores permitidos, si no "claro"
function temaActual()
{
    $tema = $_COOKIE['sim01_tema'] ?? 'claro';
    return in_array($tema, ['claro', 'oscuro'], true) ? $tema : 'claro';
}

// F) Cabecera y pie comunes
function cabecera($titulo)
{
    $tema = temaActual();
    echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">';
    echo '<title>' . e($titulo) . '</title>';
    echo '<style>
        body.claro  { background:#fff; color:#222; }
        body.oscuro { background:#222; color:#eee; }
        body.oscuro a { color:#9cf; }
        .error { color:#c00; }
        .aviso { color:#b60; }
    </style></head>';
    echo '<body class="' . e($tema) . '">';
    echo '<header><p>Hoy es ' . date('d/m/Y') . '</p>';
    echo '<nav><a href="index.php">Carta</a> | <a href="resumen.php">Resumen</a> | ';
    echo '<a href="preferencias.php">Preferencias</a></nav></header><hr>';
}

function pie()
{
    echo '<hr><footer>Cafetería &copy; ' . date('Y') . '</footer></body></html>';
}
