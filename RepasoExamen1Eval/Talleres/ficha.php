<?php
    session_start();
    require_once 'db.php';
    require_once 'funciones.php';
    comprobarSesion();
    $id = $_GET["id"] ?? '';
    if (!ctype_digit($id) || (int)$id < 1) {
        header("Location: index.php");
        exit;
    } else {
        $id = (int)$id;
        $stmt=$cn->prepare("SELECT * FROM inscripciones WHERE id = ?;");
        $stmt->bind_param('i',$id);
        $stmt->execute();
        $info=$stmt->get_result()->fetch_assoc();
    }
    

    if (!$info) {   // el id no existe en la tabla
        header("Location: index.php");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
        <p>ID: <?= e($info["id"]) ?></p>
        <p>Nombre: <?= e($info["nombre"]) ?></p>
        <p>Email: <?= e($info["email"]) ?></p>
        <p>Taller: <?= e($info["taller"]) ?></p>
        <p>Turno: <?= e($info["turno"]) ?></p>
        <p>Plazas: <?= e($info["plazas"]) ?></p>
        <p>Fecha de creación: <?= e($info["created_at"]) ?></p>
        <p>Importe: <?= euros(calcularImporte($precios[$info["taller"]],$info["plazas"])) ?></p>
        <a href="index.php">Volver</a> <br>
        <a href="nueva.php">Crear otra</a>
    </body>
</html>