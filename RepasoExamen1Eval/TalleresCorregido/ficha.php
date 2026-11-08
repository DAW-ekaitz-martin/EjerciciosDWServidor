<?php
    session_start();
    require_once 'funciones.php';
    comprobarSesion();
    require_once 'db.php';

    $error = '';
    $info = null;

    // 1) ¿Es un entero positivo? "abc", "4abc", "2.5", "0" o "" -> ID inválido
    $idTexto = $_GET["id"] ?? '';
    if (!ctype_digit($idTexto) || (int)$idTexto < 1) {
        http_response_code(400);
        $error = 'El ID indicado no es válido.';
    } else {
        // 2) ID válido: ¿existe?
        $id = (int)$idTexto;
        $stmt = $cn->prepare("SELECT * FROM inscripciones WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $info = $stmt->get_result()->fetch_assoc();

        if (!$info) {
            http_response_code(404);
            $error = 'No existe ninguna inscripción con el ID ' . $id . '.';
        }
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha de inscripción</title>
</head>
<body>
    <?php barraUsuario(); ?>
    <h1>Ficha de inscripción</h1>

<?php if ($error !== ''): ?>
    <p style="color:red"><?= e($error) ?></p>
<?php else: ?>
    <p>ID: <?= e($info["id"]) ?></p>
    <p>Nombre: <?= e($info["nombre"]) ?></p>
    <p>Email: <?= e($info["email"]) ?></p>
    <p>Taller: <?= e($info["taller"]) ?></p>
    <p>Turno: <?= $info["turno"] === 'manana' ? 'Mañana' : 'Tarde' ?></p>
    <p>Plazas: <?= e($info["plazas"]) ?></p>
    <p>Fecha de alta: <?= e(date('d/m/Y H:i', strtotime($info["created_at"]))) ?></p>
    <p>Importe: <?= euros(calcularImporte($precios[$info["taller"]] ?? 0, $info["plazas"])) ?></p>
<?php endif; ?>

    <a href="index.php">Volver al listado</a> ·
    <a href="nueva.php">Crear otra inscripción</a>
</body>
</html>
