<?php
    declare(strict_types=1);
    require_once '_comun.php';
    header("Content-Type: application/json; charset=utf-8");
    $datos = [];
    
    
    $datos['nombre'] = $_POST["nombre"];
    $datos['email'] = $_POST["email"];
    $contenido = validar_contacto($datos);
    if($contenido["nombre"] && $contenido['email']) {
        echo json_encode(['ok' => true, "datos" => $contenido]);
    }
    //No hay else porque validar contacto ya devuelve un error en caso de necesitarlo.

    
?>