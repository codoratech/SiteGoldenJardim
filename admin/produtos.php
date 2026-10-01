<?php
require_once __DIR__ . "/bootstrap.php";
require_login();
require_once __DIR__ . '/ui_helpers.php';
include('../conexao/banco.php');
$msg = ''; $msg_erro = '';
try {
if (!empty($GLOBALS['admin_form_errors'])) throw new AdminFormValidation(implode(' ', $GLOBALS['admin_form_errors']));
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && ($_POST['acao'] ?? '') !== 'excluir') {
    foreach (['Pro_Nome', 'Pro_Preco'] as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') reject_request('Preencha todos os campos obrigatórios.');
    }
}


require_once __DIR__ . "/inventory_helpers.php";

$msg = "";
$msg_erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'excluir' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    if ($id < 1) reject_request('Registro inválido.');
    if (!inventory_execute($con, 'DELETE FROM tb_produtos WHERE ID_Produto = ?', 'i', [$id])) {
        $msg_erro = "Não foi possível excluir: este produto possui registros vinculados. Detalhe: " . database_error($con);
    } else {
        $msg = "Produto excluído com sucesso!";
    }

    if (empty($msg_erro)) {
        header("Location: produtos.php?ok=1");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_produto'])) {
    $id = intval($_POST['id_produto']);
    $nome = trim($_POST['Pro_Nome']);
    $desc = $_POST['Pro_Descricao'] ?? '';
    $minimum = inventory_minimum();
    $preco = max(0, floatval(str_replace(',', '.', str_replace(['R$', ' '], '', $_POST['Pro_Preco']))));
    
    if (inventory_execute($con, 'UPDATE tb_produtos SET Pro_Nome=?, Pro_Descricao=?, Pro_Preco=?, Pro_EstoqueMinimo=? WHERE ID_Produto=?', 'ssdii', [$nome, $desc, $preco, $minimum, $id])) {
        header("Location: produtos.php?ok=update");
        exit();
    } else {
        $msg_erro = "Erro ao atualizar: " . database_error($con);
    }
}

if (isset($_GET['ok'])) {
    if ($_GET['ok'] == 'update') $msg = "Produto atualizado com sucesso!";
    if ($_GET['ok'] == '1')      $msg = "Produto excluído com sucesso!";
}


} catch (AdminFormValidation $error) { $msg_erro = $error->getMessage(); http_response_code(400); }
$edit_produto = null;
if (isset($_GET['acao']) && $_GET['acao'] == 'editar' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $edit_produto = dashboard_query($con, 'SELECT * FROM tb_produtos WHERE ID_Produto = ?', 'i', [$id])[0] ?? null;
}

include('header.php');
ui_render_page($con, 'produtos', false, $edit_produto, $msg, $msg_erro);
include('footer.php');
