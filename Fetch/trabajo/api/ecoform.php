<?php
    if(isset($_POST["nombre"]) && isset($_POST["email"])) {
        $nombre = $_POST["nombre"];
        $email = $_POST["email"];
        if(is_string($nombre) && is_string($email)) {
            $nombre = trim($nombre);
            $email = trim($email);
        } else {
           415 {'ok':true, "error" : {}};
        }

    }
    
?>