<?php
    session_start();
    require_once "funciones.php";

    // Si ya hay sesión, directamente al listado
    if (isset($_SESSION["sim02_usuario"])) {
        header("Location: index.php");
        exit;
    }

    $usuario = '';
    $error = '';

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $usuario = trim($_POST["usuario"] ?? '');
        $password = $_POST["password"] ?? '';
        if ($usuario === "admin" && $password === "1234") {
            session_regenerate_id(true);
            $_SESSION["sim02_usuario"] = $usuario;
            $_SESSION["sim02_altas"] = 0;
            header("Location: index.php");
            exit;
        } else {
            $error = "Usuario o contraseña incorrectos.";
        }
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
<?php if ($error !== ''): ?>
    <p style="color:red"><?= e($error) ?></p>
<?php endif; ?>
    <form action="login.php" method="post">
        <label for="usuario">Usuario:</label>
        <input type="text" name="usuario" id="usuario" required value="<?= e($usuario) ?>">

        <!-- La contraseña nunca se vuelve a mostrar -->
        <label for="password">Contraseña:</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Enviar</button>
    </form>
</body>
</html>
