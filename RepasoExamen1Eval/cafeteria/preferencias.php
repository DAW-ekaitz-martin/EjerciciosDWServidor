<?php
require_once 'funciones.php';

// La cookie solo se toca cuando llega un POST (nunca en una visita GET)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['restablecer'])) {
        // Borrar: mismo nombre y mismo path, con fecha pasada
        setcookie('sim01_tema', '', time() - 3600, '/');
    } else {
        $tema = $_POST['tema'] ?? '';
        if (in_array($tema, ['claro', 'oscuro'], true)) {
            setcookie('sim01_tema', $tema, [
                'expires' => time() + 7 * 24 * 60 * 60,
                'path' => '/',
                'secure' => false,   // HTTP local
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        }
    }
    // Redirigir para leer la cookie nueva en la siguiente petición
    header('Location: preferencias.php');
    exit;
}

$tema = temaActual();
cabecera('Cafetería · Preferencias');
?>
<h1>Preferencias</h1>
<p>Tema actual: <?= e($tema) ?></p>
<form action="preferencias.php" method="post">
    <label for="tema">Tema</label>
    <select name="tema" id="tema">
        <option value="claro" <?= $tema === 'claro' ? 'selected' : '' ?>>Claro</option>
        <option value="oscuro" <?= $tema === 'oscuro' ? 'selected' : '' ?>>Oscuro</option>
    </select>
    <button type="submit">Guardar</button>
</form>
<form action="preferencias.php" method="post">
    <button type="submit" name="restablecer" value="1">Restablecer tema</button>
</form>
<?php pie(); ?>
