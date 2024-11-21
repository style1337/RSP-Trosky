<?php
    session_start();

    $_SESSION = [];

    session_destroy();

    // Redirect
    header("Location: ../trosky.php");
    exit();
?>
