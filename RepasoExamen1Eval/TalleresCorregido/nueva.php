<?php
    session_start();
    require_once 'funciones.php';
    comprobarSesion(); // sin sesión no se ejecuta ningún POST
    require_once 'db.php';

    // ---------- Olvidar la preferencia de turno ----------
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["olvidarTurno"])) {
        setcookie("sim02_turno", "", time() - 3600, "/");
        header("Location: nueva.php"); // redirigir para leer el nuevo estado
        exit;
    }

    // ---------- Valores iniciales del formulario ----------
    $nombre = '';
    $email = '';
    $taller = '';
    $plazas = '';
    $errores = [];
    $mensaje = '';

    // Formulario nuevo: turno de la cookie si es válido; si no, "manana"
    $turnoCookie = $_COOKIE["sim02_turno"] ?? '';
    $turno = in_array($turnoCookie, $turnosValidos, true) ? $turnoCookie : 'manana';

    // ---------- Procesar el alta (solo POST) ----------
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $nombre = trim($_POST['nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $taller = $_POST['taller'] ?? '';
        $turnoPost = $_POST['turno'] ?? '';
        $plazas = trim($_POST['plazas'] ?? ''); // se valida como texto ANTES de convertir

        if ($nombre === '') {
            $errores[] = 'El nombre es obligatorio.';
        } elseif (mb_strlen($nombre) > 100) {
            $errores[] = 'El nombre no puede superar 100 caracteres.';
            $nombre = ''; // no se conserva un campo inválido
        }

        if ($email === '') {
            $errores[] = 'El email es obligatorio.';
        } elseif (mb_strlen($email) > 120) {
            $errores[] = 'El email no puede superar 120 caracteres.';
            $email = '';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El email no tiene un formato válido.';
            $email = '';
        }

        if (!array_key_exists($taller, $precios)) {
            $errores[] = 'Elige un taller del catálogo.';
            $taller = '';
        }

        // Si el POST trae un turno válido se conserva; si no, error y se queda el de la cookie
        if (in_array($turnoPost, $turnosValidos, true)) {
            $turno = $turnoPost;
        } else {
            $errores[] = 'El turno debe ser mañana o tarde.';
        }

        // "2.5", "abc", "" o "-1" no pasan ctype_digit
        if (!ctype_digit($plazas) || (int)$plazas < 1 || (int)$plazas > 4) {
            $errores[] = 'Las plazas deben ser un número entero entre 1 y 4.';
            $plazas = '';
        }

        // ---------- Insertar solo si no hay errores ----------
        if (count($errores) === 0) {
            $plazasInt = (int)$plazas;
            try {
                $stmt = $cn->prepare("INSERT INTO inscripciones (nombre, email, taller, turno, plazas) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssi", $nombre, $email, $taller, $turno, $plazasInt);
                $stmt->execute();
                $id = $cn->insert_id;

                // Solo tras un alta correcta: cookie de turno y contador
                setcookie("sim02_turno", $turno, time() + 30 * 24 * 60 * 60, "/");
                $_SESSION["sim02_altas"]++;

                header("Location: ficha.php?id=" . $id);
                exit;
            } catch (mysqli_sql_exception $e) {
                // 1062 = clave duplicada (email UNIQUE). Nunca se muestra el error interno.
                if ($e->getCode() === 1062) {
                    $mensaje = 'No se ha podido completar el alta: ya existe una inscripción con ese email.';
                    $email = '';
                } else {
                    $mensaje = 'No se ha podido completar el alta. Inténtalo de nuevo.';
                }
            }
        }
    }

    $hayTurnoRecordado = in_array($turnoCookie, $turnosValidos, true);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva inscripción</title>
</head>
<body>
    <?php barraUsuario(); ?>
    <h1>Nueva inscripción</h1>

<?php if ($mensaje !== ''): ?>
    <p style="color:red"><?= e($mensaje) ?></p>
<?php endif; ?>
<?php if ($errores): ?>
    <ul style="color:red">
    <?php foreach ($errores as $error): ?>
        <li><?= e($error) ?></li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

    <form action="nueva.php" method="post">
        <label for="nombre">Nombre</label>
        <input type="text" name="nombre" id="nombre" maxlength="100" value="<?= e($nombre) ?>"><br><br>

        <label for="email">Email</label>
        <input type="email" name="email" id="email" maxlength="120" value="<?= e($email) ?>"><br><br>

        <label for="taller">Taller</label>
        <select name="taller" id="taller">
            <option value="">Seleccionar…</option>
<?php foreach ($precios as $clave => $precio): ?>
            <option value="<?= e($clave) ?>" <?= $taller === $clave ? 'selected' : '' ?>><?= e(strtoupper($clave)) ?> (<?= euros($precio) ?>)</option>
<?php endforeach; ?>
        </select><br><br>

        <label for="turno">Turno</label>
        <select name="turno" id="turno">
            <option value="manana" <?= $turno === 'manana' ? 'selected' : '' ?>>Mañana</option>
            <option value="tarde" <?= $turno === 'tarde' ? 'selected' : '' ?>>Tarde</option>
        </select><br><br>

        <label for="plazas">Plazas</label>
        <input type="number" name="plazas" id="plazas" min="1" max="4" value="<?= e($plazas) ?>"><br><br>

        <button type="submit">Crear inscripción</button>
    </form><br>

<?php if ($hayTurnoRecordado): ?>
    <form action="nueva.php" method="post">
        <p>Turno recordado: <?= $turnoCookie === 'manana' ? 'Mañana' : 'Tarde' ?></p>
        <button type="submit" name="olvidarTurno" value="1">Olvidar turno recordado</button>
    </form>
<?php endif; ?>
</body>
</html>
