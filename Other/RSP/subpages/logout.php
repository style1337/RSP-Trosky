<?php
    session_start();

    $_SESSION = [];

    if (session_destroy()) {
        $_SESSION['success'] = "Úspěšně jste se odhlásili.";
    } else {
        $_SESSION['error'] = "Došlo k chybě při odhlašování.";
    }

    // Redirect
    header("Location: ../trosky.php");
    exit();
?>