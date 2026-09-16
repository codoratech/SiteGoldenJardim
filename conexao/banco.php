<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "golden_jardim_db";

// Conecta ao MySQL sem especificar o banco para poder criá-lo caso não exista
$con = mysqli_connect($host, $user, $pass);

if (!$con) {
    die("Falha ao se conectar ao MySQL: " . mysqli_connect_error());
}

// Cria o banco de dados se não existir
$sql_db = "CREATE DATABASE IF NOT EXISTS `$db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if (mysqli_query($con, $sql_db)) {
    mysqli_select_db($con, $db);
    
    // Verifica se as tabelas já existem; caso contrário, importa o banco.sql automaticamente
    $check = mysqli_query($con, "SHOW TABLES LIKE 'tb_login'");
    if ($check && mysqli_num_rows($check) == 0) {
        $sql_file = __DIR__ . "/../banco.sql";
        if (file_exists($sql_file)) {
            $sql_content = file_get_contents($sql_file);
            if (mysqli_multi_query($con, $sql_content)) {
                do {
                    // Limpa os resultados do multi_query
                } while (mysqli_more_results($con) && mysqli_next_result($con));
            }
        }
    }
}

mysqli_set_charset($con, "utf8mb4");
?>
