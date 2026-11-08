<?php
    session_start();
    require_once 'db.php';
    require_once 'funciones.php';
    comprobarSesion();
    if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["eliminarPreferencia"])) {
        setcookie("sim02_turno", "", time()-3600, "/");
        header("Location: nueva.php"); // redirigir para leer el nuevo estado
        exit;
    }

    // Valores del formulario (para repintarlo tras un error)
    $nombre = '';
    $email = '';
    $taller = '';
    $turno = '';
    $plazas = '';
    $errores = [];
    $mensaje = '';

    // Formulario nuevo: turno de la cookie si es válido; si no, "manana"
    $turnoCookie = $_COOKIE["sim02_turno"] ?? '';
    $turno = in_array($turnoCookie, $turnosValidos, true) ? $turnoCookie : 'manana';

    if($_SERVER["REQUEST_METHOD"]=== "POST") {
        $nombre = trim($_POST['nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $taller = trim($_POST['taller'] ?? '');
        $turno = trim($_POST['turno'] ?? '');
        $plazas = trim($_POST['plazas'] ?? '');

        if ($nombre === '') {
            $errores[] = 'El nombre es obligatorio.';
        } elseif (mb_strlen($nombre) > 100) {
            $errores[] = 'El nombre no puede superar 100 caracteres.';
            $nombre = ''; // no se conserva un campo inválido
        }

        if ($email === '') {
            $errores[] = 'El email es obligatorio.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El email no tiene un formato válido.';
            $email = '';
        } else if(mb_strlen($email) > 120) {
            $errores[] = 'El email no puede superar 120 caracteres.';
            $email = '';
        }

        if ($taller === '') {
            $errores[] = 'El taller es obligatorio.';
        }
        //Comprobar que el taller ingresado se encuentra dentro de las opciones
        if (!array_key_exists($taller, $precios)) {
            $errores[] = 'Elige un taller del catálogo.';
            $taller = '';
        }

        // Si el POST trae un turno válido se conserva; si no, error y se queda el de la cookie
        if (in_array($turno, $turnosValidos, true)) {
            $turno = $turno;
        } else {
            $errores[] = 'El turno debe ser mañana o tarde.';
        }
        if ($turno === '') {
            $errores[] = 'El turno es obligatorio.';
        }

        // "2.5", "abc", "" o "-1" no pasan ctype_digit
        if (!ctype_digit($plazas) || (int)$plazas < 1 || (int)$plazas > 4) {
            $errores[] = 'Las plazas deben ser un número entero entre 1 y 4.';
            $plazas = '';
        }
        if ($plazas === '') {
            $errores[] = 'El numero de plazas es obligatorio.';
        }

        //Hacer la inserción en la base de datos
        if(count($errores) == 0) {
            try {

                $cn->begin_transaction();

                // Comprobar email estado
                $stmt = $cn->prepare("SELECT * FROM inscripciones WHERE email = ?;");

                $stmt->bind_param("s", $email);
                $stmt->execute();
                $res=$stmt->get_result();

                // Añadir inscripción
                if($res->num_rows > 0) {
                    throw new Exception("El email ya existe, no se pudo crear la inscripcion");
                }
                $stmt2 = $cn->prepare("INSERT INTO inscripciones (nombre, email, taller, turno, plazas) VALUES (?,?,?,?,?);");

                $stmt2->bind_param(
                    "ssssi",$nombre,$email,$taller,$turno,$plazas);

                $stmt2->execute();
                $id = $cn->insert_id;
                $cn->commit();
                setcookie("sim02_turno", $turno, time()+2592000,"/");
                $_SESSION["sim02_altas"] ++;
                header("Location: ficha.php?id=" . $id);
                exit;
            } catch (Exception $e) {

                $cn->rollback();
                $mensaje = $e->getMessage();
            }
        }
    }

    $hayTurnoRecordado = in_array($turnoCookie, $turnosValidos, true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Nueva Incripción</h1>
    <form action="nueva.php" method="post">
        <label for="nombre">Nombre</label>
        <input type="text" name="nombre" id="nombre" required maxlength="100" value="<?= e($nombre) ?>"><br><br>

        <label for="email" >Email</label>
        <input type="email" name="email" id="email" maxlength="120" value="<?= e($email) ?>"><br><br>
        
        <label for="taller">Taller</label>
        <select name="taller" id="taller" >
            <option value="">Seleccionar…</option>
            <option value="php">PHP</option>
            <option value="css">CSS</option>
            <option value="mysql">MySQL</option>
        </select><br><br>

        <label for="turno">Turno</label>
        <select name="turno" id="turno" value="<?= $_COOKIE["sim02_turno"] ?? "manana"?>">
            <option value="">Seleccionar…</option>
            <option value="manana"<?= $turno === 'manana' ? 'selected' : '' ?>>Mañana</option><!--Se lee así: «¿$turno vale 'manana'? Si sí, el resultado es 'selected'; si no, el resultado es '' (nada)». Esto sirve para dejarlo preseleccionado en caso de que la cookie esté activa-->
            <option value="tarde"<?= $turno === 'tarde'  ? 'selected' : '' ?>>Tarde</option><!--El mismo patrón sirve para el resto de controles: en caso de ser de type radio o checkbox, el atributo a añadir sería checked -->
        </select><br><br>

        <label for="plazas">Plazas</label>
        <input type="number" name="plazas" id="plazas"  min="1" max="4" value="<?= e($plazas) ?>"><br><br>

        <button type="submit">Crear inscripcion</button>
    </form><br><br>
    <p><?= $mensaje ?></p>
    <?php if ($errores): ?>
    <h3>Criterios a cumplir:</h3>
    <ul class="error">
    <?php foreach ($errores as $error): ?>
        <li><?= e($error) ?></li>
    <?php endforeach; ?>
    </ul>
    <?php endif; ?><br><br>
    <a href="index.php">Volver</a>
<?php
    if($hayTurnoRecordado) {
?>
        <form action="nueva.php" method="post">
            <input type="text" name="eliminarPreferencia" hidden>
            <button type="submit">Eliminar preferencia</button>
        </form>
<?php
    }
?>
    
</body>
</html>