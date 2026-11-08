<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
//En casa
$host = '127.0.0.1';
$port = 3307;
$user = 'root'; // En XAMPP, normalmente 'root'
$pass = 'root'; // En XAMPP, normalmente ''
$db = 'sim02_talleres';
$cn = new mysqli($host,$user, $pass, $db, $port);
$cn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
// En producción, registra el error y muestra un mensaje genérico.
    echo "Error de conexión con la base de datos";
exit('Error de conexión a la base de datos.');
}
