<?php
    session_start();

    // Vaciar y destruir la sesión (el contador sim02_altas desaparece)
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600, $p["path"], $p["domain"], $p["secure"], $p["httponly"]);
    }
    session_destroy();

    // La cookie sim02_turno NO se toca: el turno recordado se conserva
    header("Location: login.php");
    exit;
