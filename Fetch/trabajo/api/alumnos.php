<?php
    header('Content-Type: text/plain; charset=utf-8'); 
    $alumnos = [
        ['id'=>1, 'nombre' =>"Ana López"],
        ['id'=>2, 'nombre' =>"Luis Pérez"],
        ['id'=>3, 'nombre' =>"Marta Gil"],
    ];
    echo json_encode(['ok' => true, 'datos'=> $alumnos], 
    JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
?>