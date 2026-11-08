<?php
session_start();
require_once 'funciones.php';

if (!isset($_SESSION['sim01_contador'])) {
    $_SESSION['sim01_contador'] = 0;
}

// ---------- A) Preselección por GET ----------
$aviso = '';
$menu = 'basico';
if (isset($_GET['menu'])) {
    if (array_key_exists($_GET['menu'], $precios)) {
        $menu = $_GET['menu'];
    } else {
        $aviso = 'El menú indicado no existe. Se ha seleccionado el básico.';
    }
}

// Valores del formulario (para repintarlo tras un error)
$nombre = '';
$email = '';
$unidadesTexto = '';
$entrega = '';
$socio = false;
$errores = [];

// ---------- B) Procesar el POST ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $menuPost = $_POST['menu'] ?? '';
    $unidadesTexto = trim($_POST['unidades'] ?? '');
    $entrega = $_POST['entrega'] ?? '';
    $socio = isset($_POST['socio']);

    if ($nombre === '') {
        $errores[] = 'El nombre es obligatorio.';
    } elseif (mb_strlen($nombre) > 80) {
        $errores[] = 'El nombre no puede superar 80 caracteres.';
        $nombre = ''; // no se conserva un campo inválido
    }

    if ($email === '') {
        $errores[] = 'El email es obligatorio.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El email no tiene un formato válido.';
        $email = '';
    }

    // El menú del POST prevalece sobre el del GET
    if (array_key_exists($menuPost, $precios)) {
        $menu = $menuPost;
    } else {
        $errores[] = 'Elige un menú válido.';
    }

    // Comprobar ANTES de convertir: solo dígitos -> "2.5", "2abc", "dos", "" fallan
    if ($unidadesTexto === '' || !ctype_digit($unidadesTexto)) {
        $errores[] = 'Las unidades deben ser un número entero.';
        $unidadesTexto = '';
    } else {
        $unidades = (int)$unidadesTexto;
        if ($unidades < 1 || $unidades > 6) {
            $errores[] = 'Las unidades deben estar entre 1 y 6.';
            $unidadesTexto = '';
        }
    }

    if (!in_array($entrega, ['local', 'envio'], true)) {
        $errores[] = 'Elige una forma de entrega válida.';
        $entrega = '';
    }

    // ---------- C) + D) Solo si todo es correcto ----------
    if (count($errores) === 0) {
        $precioUnitario = $precios[$menu]; // el precio sale del servidor
        $calculo = calcularPedido($precioUnitario, $unidades, $socio, $entrega);

        $_SESSION['sim01_pedido'] = [
            'nombre' => $nombre,
            'email' => $email,
            'menu' => $menu,
            'precio' => $precioUnitario,
            'unidades' => $unidades,
            'entrega' => $entrega,
            'socio' => $socio,
            'calculo' => $calculo
        ];
        $_SESSION['sim01_contador']++;

        header('Location: resumen.php'); // PRG: recargar el resumen no repite el pedido
        exit;
    }
}

cabecera('Cafetería · Carta');
?>
<h1>Nuestra carta</h1>
<ul>
<?php foreach ($precios as $clave => $precio): ?>
    <li>
        <?= e($nombresMenu[$clave]) ?> — <?= euros($precio) ?>
        <a href="index.php?menu=<?= urlencode($clave) ?>">Pedir este</a>
    </li>
<?php endforeach; ?>
</ul>

<?php if ($aviso !== ''): ?>
    <p class="aviso"><?= e($aviso) ?></p>
<?php endif; ?>

<?php if ($errores): ?>
    <ul class="error">
    <?php foreach ($errores as $error): ?>
        <li><?= e($error) ?></li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

<h2>Haz tu pedido</h2>
<form action="index.php" method="post">
    <p>
        <label for="nombre">Nombre</label>
        <input type="text" name="nombre" id="nombre" maxlength="80" value="<?= e($nombre) ?>">
    </p>
    <p>
        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="<?= e($email) ?>">
    </p>
    <p>
        <label for="menu">Menú</label>
        <select name="menu" id="menu">
        <?php foreach ($nombresMenu as $clave => $texto): ?>
            <option value="<?= e($clave) ?>" <?= $clave === $menu ? 'selected' : '' ?>><?= e($texto) ?></option>
        <?php endforeach; ?>
        </select>
    </p>
    <p>
        <label for="unidades">Unidades (1-6)</label>
        <input type="number" name="unidades" id="unidades" min="1" max="6" value="<?= e($unidadesTexto) ?>">
    </p>
    <p>Entrega:
        <label><input type="radio" name="entrega" value="local" <?= $entrega === 'local' ? 'checked' : '' ?>> Recoger en local</label>
        <label><input type="radio" name="entrega" value="envio" <?= $entrega === 'envio' ? 'checked' : '' ?>> Envío</label>
    </p>
    <p>
        <label><input type="checkbox" name="socio" value="1" <?= $socio ? 'checked' : '' ?>> Soy socio</label>
    </p>
    <button type="submit">Realizar pedido</button>
</form>
<?php pie(); ?>
