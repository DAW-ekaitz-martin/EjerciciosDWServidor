<?php
    session_start();
    require_once "db.php";
    if(isset($_SESSION["sim02_usuario"])) {
        header("Location:index.php");
        exit;
    }
    $usuario = '';
    $error = '';

        if($_SERVER["REQUEST_METHOD"] === "POST") {
            $usuario = trim($_POST["usuario"] ?? '');
            $password = $_POST["password"] ?? "";
            if($usuario === "admin" && $password === "1234") {
                $_SESSION["sim02_usuario"] = $usuario;
                $_SESSION["sim02_altas"] = 0;
                header("Location:index.php");
                exit;
            } else {
                echo "Usuario o contraeña incorrectos.";
            }
        }
?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Login</title>
        </head>
        <body>
            <h1>Login</h1>
<?php 
                if ($error !== ''): 
?>
                <p style="color:red"><?= e($error) ?></p>
<?php 
                endif; 
?>
            <form action="login.php" method="post">
                <label for="usuario">Usuario:</label>
                <input type="text" name="usuario" required>

                <label for="password">Contraseña:</label>
                <input type="password" name="password" required>

                <button type="submit" name="login">Enviar</button>

            </form>
</body>  
</html>