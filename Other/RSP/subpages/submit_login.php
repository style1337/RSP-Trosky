<?php
session_start();
require("connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = md5($_POST['password']);

        $sql = "select * from troskopis_users where username='$username' AND password='$password' ";
        $vysledek = mysqli_query($spojeni, $sql);

        if ( mysqli_num_rows($vysledek) > 0 )
      	{
     		$zaznam = mysqli_fetch_assoc($vysledek);
  		  	header("Cache-control: private");
   			 $_SESSION["user_id"] = $zaznam['user_id'];
  			 $_SESSION["role"] = $zaznam['role'] ;
   			 $_SESSION["username"] = $zaznam['username'] ;
   			header("Location: ../trosky.php");
   			exit();
     	} 
        
        else {
            $_SESSION['error'] = "Neplatné jméno nebo heslo!";
            header('Location: login.php');
            exit();
        }
    }
}
?>
