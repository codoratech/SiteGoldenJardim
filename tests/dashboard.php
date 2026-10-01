<?php
// Testa a consulta real em banco isolado. Nunca utiliza config.local.php.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$con = mysqli_connect('127.0.0.1', 'root', '', '', 3308);
mysqli_set_charset($con, 'utf8mb4');
mysqli_query($con, 'CREATE DATABASE IF NOT EXISTS gj_dashboard_tests CHARACTER SET utf8mb4');
mysqli_select_db($con, 'gj_dashboard_tests');
function run_sql($con, $sql) {
    mysqli_multi_query($con, $sql);
    do { if ($result = mysqli_store_result($con)) mysqli_free_result($result); } while (mysqli_more_results($con) && mysqli_next_result($con));
}
function database_error($con) { return mysqli_error($con); }
function check($condition, $message) { if (!$condition) throw new RuntimeException($message); }
require __DIR__ . '/../admin/dashboard_data.php';
if (!mysqli_num_rows(mysqli_query($con, "SHOW TABLES LIKE 'tb_clientes'"))) {
    $schema = file_get_contents(__DIR__ . '/../.deploy/infinityfree-2026-09-30/banco-escola.sql');
    $schema = preg_replace('/^INSERT INTO .*;\R/m', '', $schema);
    run_sql($con, $schema);
}
if (!dashboard_stock($con)['ready']) run_sql($con, file_get_contents(__DIR__ . '/../migrations/002_dashboard_estoque.sql'));
$empty = dashboard_data($con);
check((int) $empty['counts']['clientes'] === 0, 'O banco de testes precisa estar vazio.');
check(array_sum($empty['os']) === 0 && !$empty['top'], 'Estados vazios incorretos.');
check(count($empty['labels']) === 6, 'O eixo temporal deve ter seis meses.');
mysqli_begin_transaction($con);
try {
    run_sql($con, file_get_contents(__DIR__ . '/dashboard-exemplos.sql'));
    $data = dashboard_data($con); $stock = dashboard_stock($con);
    check((int) $data['counts']['ordens'] === 6 && array_sum($data['os']) === 6, 'Ordens mensais incorretas.');
    check($data['status']['Concluída'] === 3 && $data['status']['Pendente'] === 1, 'Distribuição por status incorreta.');
    check($data['received'][5] === 300.0 && $data['pending'][5] >= 150.0 && $data['pending'][5] <= 200.0, 'Contas canceladas contam ou soma incorreta.');
    check(count($data['top']) === 1 && (int) $data['top'][0]['total'] === 2, 'Top serviços incorreto.');
    check(count($data['appointments']) === 1 && $data['appointments'][0]['servico'] === 'Poda demonstrativa', 'Agenda não vinculou o serviço.');
    check(count($stock['alerts']) === 2 && $stock['critical'] === 1, 'Classificação dos alertas incorreta.');
    check((int) $stock['alerts'][0]['saldo'] === 1 && (int) $stock['alerts'][1]['saldo'] === 8, 'Saídas não foram subtraídas.');
    mysqli_query($con, 'UPDATE tb_produtos SET Pro_EstoqueMinimo = NULL');
    check(!dashboard_stock($con)['alerts'], 'Produtos sem mínimo geram alertas.');
    echo "Dashboard: estados vazios, seis meses, status, financeiro, serviços, agenda e estoque aprovados.\n";
} finally { mysqli_rollback($con); }
