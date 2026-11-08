<?php
    session_start();
    if(isset($_SESSION["sim02_usuario"])) {
        $_SESSION = [];
        session_destroy();        
        header("Location:login.php");
        exit;
    }
?>