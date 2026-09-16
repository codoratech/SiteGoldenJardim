<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "golden_jardim_db";

$con = mysqli_connect($host, $user, $pass, $db);

if (mysqli_connect_errno()) {
    die("Falha ao se conectar ao MySQL: " . mysqli_connect_error());
}

mysqli_set_charset($con, "utf8mb4");
?>
