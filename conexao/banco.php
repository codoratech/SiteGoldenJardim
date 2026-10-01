<?php
require_once __DIR__ . '/connection.php';
$con = gj_open_connection();
if (!$con) {
    http_response_code(503);
    exit('Não foi possível conectar ao banco de dados. Verifique a configuração da instalação.');
}
function database_error($connection) {
    error_log('Golden Jardim: ' . mysqli_error($connection));
    return 'Não foi possível concluir a operação. Confira os dados e os registros vinculados.';
}
