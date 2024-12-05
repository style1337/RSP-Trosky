<?php
    session_start();
    require("connect.php");

    // Zkontrolování, zda je uživatel přihlášen a má příslušnou roli
    if ($_SESSION['role'] != 'admin') {
        // Pokud uživatel není přihlášen nebo nemá příslušnou roli, přesměrování na stránku s chybou
        header("Location: unauthorized.php");
        exit();
    } 
?>