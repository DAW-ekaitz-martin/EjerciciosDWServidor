<?php
    //Precios
    $precios = [
        "php" => 18,
        "css" => 15,
        "mysql" => 20,
    ];
    //Mantener el turno en caso fallo del formulario  
    $turnosValidos = ['manana', 'tarde'];

    // Escapar texto para HTML (contenido y atributos)
    function e($texto)
    {
        return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
    }

    // Formato solo para mostrar: 27.8 -> "27,80 €"
    function euros($importe)
    {
        return number_format($importe, 2, ',', '.') . ' €';
    }
    function calcularImporte($precio, $numPlazas) {
        $total = $precio * $numPlazas;
        return $total;
    }
    // Protege las páginas privadas: sin sesión, al login (también para los POST)
    function comprobarSesion() {
        if (!isset($_SESSION["sim02_usuario"])) {
            header("Location: login.php");
            exit;
        }
        if (!isset($_SESSION["sim02_altas"])) {
            $_SESSION["sim02_altas"] = 0;
        }
    }
?>