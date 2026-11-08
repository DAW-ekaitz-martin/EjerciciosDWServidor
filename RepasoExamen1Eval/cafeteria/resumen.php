<?php
session_start();
require_once 'funciones.php';

// "Olvidar último pedido": solo POST, se procesa ANTES del HTML y se redirige
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['olvidar'])) {
    unset($_SESSION['sim01_pedido']); // el contador NO se toca
    header('Location: resumen.php');
    exit;
}

$pedido = $_SESSION['sim01_pedido'] ?? null;
$contador = $_SESSION['sim01_contador'] ?? 0;

cabecera('Cafetería · Resumen');
?>
<h1>Resumen del último pedido</h1>
<p>Pedidos aceptados: <?= (int)$contador ?></p>

<?php if ($pedido === null): ?>
    <p>No hay ningún pedido guardado.</p>
    <p><a href="index.php">Volver a la carta</a></p>
<?php else: ?>
    <p>Cliente: <?= e($pedido['nombre']) ?> (<?= e($pedido['email']) ?>)</p>
    <p>Menú: <?= e($nombresMenu[$pedido['menu']]) ?> — <?= euros($pedido['precio']) ?>/ud.</p>
    <p>Unidades: <?= (int)$pedido['unidades'] ?></p>
    <p>Entrega: <?= $pedido['entrega'] === 'envio' ? 'Envío' : 'Recoger en local' ?>
       · Socio: <?= $pedido['socio'] ? 'Sí' : 'No' ?></p>
    <table border="1" cellpadding="4">
        <tr><td>Subtotal</td><td><?= euros($pedido['calculo']['subtotal']) ?></td></tr>
        <tr><td>Descuento</td><td><?= euros($pedido['calculo']['descuento']) ?></td></tr>
        <tr><td>Envío</td><td><?= euros($pedido['calculo']['envio']) ?></td></tr>
        <tr><th>Total</th><th><?= euros($pedido['calculo']['total']) ?></th></tr>
    </table>
    <form action="resumen.php" method="post">
        <button type="submit" name="olvidar" value="1">Olvidar último pedido</button>
    </form>
<?php endif; ?>
<?php pie(); ?>
