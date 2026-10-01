<?php
mysqli_report(MYSQLI_REPORT_OFF);
$local = __DIR__ . '/config.local.php';
$config = is_file($local) ? require $local : [];
$host = getenv('GJ_DB_HOST') ?: ($config['host'] ?? 'localhost');
$user = getenv('GJ_DB_USER') ?: ($config['user'] ?? 'root');
$password = getenv('GJ_DB_PASSWORD');
if ($password === false) $password = $config['password'] ?? '';
$database = getenv('GJ_DB_NAME') ?: ($config['database'] ?? 'golden_jardim_db');
$port = (int) (getenv('GJ_DB_PORT') ?: ($config['port'] ?? 3306));
$con = @mysqli_connect($host, $user, $password, $database, $port);
if (!$con || !mysqli_set_charset($con, 'utf8mb4')) {
    error_log('Golden Jardim: falha de conexão com o banco de dados.');
    http_response_code(503);
    exit('Não foi possível conectar ao banco de dados. Verifique a configuração da instalação.');
}
function database_error($connection) {
    error_log('Golden Jardim: ' . mysqli_error($connection));
    return 'Não foi possível concluir a operação. Confira os dados e os registros vinculados.';
}
