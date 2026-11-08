<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    session_start();
    require_once "db.php";
    require_once "funciones.php";
    comprobarSesion();
    
    $stmt= $cn->prepare("SELECT * FROM inscripciones;");//Por defecto mostramos todas
    if($_SERVER["REQUEST_METHOD"] === "GET"){
        if(isset($_GET["filtroNombre"]) && isset($_GET["filtroTaller"])) {
            $nombre = $_GET["filtroNombre"];
            $q="%{$nombre}%";
            $taller = $_GET["filtroTaller"];
            $stmt= $cn->prepare("SELECT * FROM inscripciones WHERE nombre LIKE ? AND taller = ?;");
            $stmt->bind_param('ss', $q,$taller);
        } else if(isset($_GET["filtroNombre"])) {
            $nombre = $_GET["filtroNombre"];
            $q="%{$nombre}%";
            $stmt= $cn->prepare("SELECT * FROM inscripciones WHERE nombre LIKE ?;");
            $stmt->bind_param('s', $q);
        } else if(isset($_GET["filtroTaller"])){
            $taller = $_GET["filtroTaller"];
            $stmt= $cn->prepare("SELECT * FROM inscripciones WHERE taller = ?;");
            $stmt->bind_param('s', $taller);
        }
    }
    $stmt->execute();
    $res = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Incripciones existentes</h1>
    <p>Total de altas en sesión: <?= $_SESSION["sim02_altas"] ?></p>
    <p>Usuario logeado en sesión: <?= $_SESSION["sim02_usuario"] ?></p>
    <a href="logout.php">Cerrar Sesión</a><br><br>
    <a href="nueva.php">Nueva Incripción</a><br><br>
    <hr>
    <form action="index.php" method="get">
        <label for="filtroNombre">Nombre</label>
        <input type="text" name="filtroNombre">
        <button type="submit">Filtrar</button>
    </form><br><br>
    <form action="index.php" method="get">
        <label for="filtroTaller">Taller</label>
        <select type="text" name="filtroTaller">
            <option value="">Selecciona un taller</option>
            <option value="php">PHP</option>
            <option value="css">CSS</option>
            <option value="mysql">MySQL</option>
        </select>
        <button type="submit">Filtrar</button>
    </form>
    <hr>
<?php
    if($res->num_rows > 0) {
        $numPlazas = 0;
        $precioTotal = 0;
?>
        <table border="2" cellpadding="6">
            <tr>
                <th>ID</th><th>Nombre</th><th>Email</th><th>Taller</th><th>Turno</th><th>Plazas</th><th>Precio</th><th>Detalle</th>
            </tr>
<?php
            while($r=$res->fetch_assoc()) {
?>
                <tr>
                    <td><?= e($r["id"]) ?></td>
                    <td><?= e($r["nombre"]) ?></td>
                    <td><?= e($r["email"]) ?></td>
                    <td><?= e($r["taller"]) ?></td>
                    <td><?= e($r["turno"]) ?></td>
                    <td><?= e($r["plazas"]) ?></td>
                    <td><?= euros(calcularImporte($precios[$r["taller"]],$r["plazas"])) ?></td>
                    <td><a href="ficha.php?id=<?= e($r["id"]) ?>" >Detalle</a></td>
                </tr>
<?php
                $numPlazas+=$r["plazas"];
                $precioTotal+=calcularImporte($precios[$r["taller"]],$r["plazas"]);
            }
?>
    </table><br>
    <p>Numero de inscripciones totales: <?= $res->num_rows ?></p>
    <p>Numero de plazas totales: <?= $numPlazas ?></p>
    <p>Importe total: <?= euros($precioTotal) ?></p>

<?php
        if(isset($_GET["filtroNombre"]) || isset($_GET["filtroTaller"])) {
?>
            <form action="index.php" method="post">
                <button type="submit">Limpiar filtros</button>
            </form>
<?php
        }
    } else if($res->num_rows == 0 && (isset($_GET["filtroNombre"]) || isset($_GET["filtroTaller"]))) {
?>
        <form action="index.php" method="post">
                <button type="submit">Limpiar filtros</button>
        </form> 
<?php  
        echo "No existen coincidencias para los filtros aplicados";
    }
    else {
        echo "No existen inscripciones en la base de datos";
    }
?>
</body>
</html>