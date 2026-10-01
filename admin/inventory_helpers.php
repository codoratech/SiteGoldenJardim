<?php
require_once __DIR__ . '/dashboard_data.php';
function inventory_execute($con, $sql, $types, $params) {
    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) return false;
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}
function inventory_minimum() {
    $value = trim($_POST['Pro_EstoqueMinimo'] ?? '');
    if ($value === '') return null;
    if (!ctype_digit($value) || (float) $value > 2147483647) reject_request('O estoque mínimo deve ser um inteiro entre zero e 2147483647.');
    return (int) $value;
}
function inventory_movement($con) {
    $type = trim($_POST['Est_Tipo'] ?? '');
    $quantity = $_POST['Est_Quantidade'] ?? '';
    $product = trim($_POST['TB_Produtos_ID_Produto'] ?? '');
    if ($type === '' || strlen($type) > 20) reject_request('Informe um tipo de movimentação válido, com até 20 caracteres.');
    if (!ctype_digit($quantity) || (float) $quantity > 2147483647) reject_request('Informe uma quantidade inteira válida.');
    $product = $product === '' ? null : (int) $product;
    if ($product !== null) {
        if (!in_array($type, ['Entrada', 'Saida'], true)) reject_request('Para um produto vinculado, escolha Entrada ou Saída.');
        if (!dashboard_query($con, 'SELECT ID_Produto FROM tb_produtos WHERE ID_Produto = ?', 'i', [$product])) reject_request('O produto selecionado não existe.');
    }
    return [$type, (int) $quantity, $product];
}
