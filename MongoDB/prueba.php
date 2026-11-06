<?php
require __DIR__ . '/vendor/autoload.php';
use MongoDB\Client;
$cliente = new Client('mongodb://localhost:27017');
$col = $cliente->escuela->alumnos;
$col->insertOne([
'nombre' => 'Ane',
'curso' => '1ºDAW'
]);
$resultado = $col->find();
foreach ($resultado as $doc) {
print_r($doc);
}