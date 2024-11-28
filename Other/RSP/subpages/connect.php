<?php
$servername = "localhost";
/*$username = "root";
$password = "";*/
$username = "root";
$password = "";
$dbname = "kriz17";
$spojeni = mysqli_connect($servername, $username, $password, $dbname);
 mysqli_set_charset($spojeni, "utf8");
?>