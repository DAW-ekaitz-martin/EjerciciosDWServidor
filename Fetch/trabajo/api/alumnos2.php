<?php
    header('Content-Type: text/plain; charset=utf-8'); 
    $alumnos = [
        ['id'=>1, 'nombre' =>"Ana López"],
        ['id'=>2, 'nombre' =>"Luis Pérez"],
        ['id'=>3, 'nombre' =>"Marta Gil"],
    ];
    $alumnosfiltrado = [];
    $param = trim($_GET["q"] ?? "");
    foreach($alumnos as $alumno) {
        if(str_contains(mb_strtolower($param, 'UTF-8'),mb_strtolower($alumno['nombre'], 'UTF-8'))) {
            $alumnosfiltrado = $alumno;
        }
    }
    echo json_encode(['ok' => true, 'datos'=> $alumnosfiltrado], 
    JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
?>