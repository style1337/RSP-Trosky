<?php
    session_start();
    require_once("connect.php");

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = md5($_POST['password']);

        $checkQuery = "SELECT * FROM troskopis_users WHERE username='$username'";
        $checkVysledek = mysqli_query($spojeni, $checkQuery);
            //printf("Hello World");

        if (mysqli_num_rows($checkVysledek) > 0) {
            $_SESSION['error'] = "Zadaný uživatel již existuje!";
            header('Location: register.php');
            exit();
        }

        else {
            $sql = "INSERT INTO troskopis_users (user_id, username, email, password) VALUES (NULL, '$username', '$email', '$password')";
            $vysledek = mysqli_query($spojeni, $sql);
        }
        
        if ( $vysledek == TRUE ) {
             $_SESSION["success"] = "Úspěšná registrace, můžete se přihlásit";
             header("Location: login.php");
             exit();
        }
        
        else {
          $_SESSION["error"] = "Registrace se nezdařila";
          header("Location: register.php");
          exit();
      }


    }


    }

?>