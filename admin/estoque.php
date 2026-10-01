<?php
require_once __DIR__ . "/bootstrap.php";
require_login();
require_once __DIR__ . '/ui_helpers.php';
include('../conexao/banco.php');
$msg = ''; $msg_erro = '';
try {
if (!empty($GLOBALS['admin_form_errors'])) throw new AdminFormValidation(implode(' ', $GLOBALS['admin_form_errors']));
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && ($_POST['acao'] ?? '') !== 'excluir') {
    foreach (['Est_Tipo', 'Est_Quantidade'] as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') reject_request('Preencha todos os campos obrigatórios.');
    }
}


require_once __DIR__ . "/inventory_helpers.php";

$msg = "";
$msg_erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'excluir' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    if ($id < 1) reject_request('Registro inválido.');
    if (!inventory_execute($con, 'DELETE FROM tb_estoque WHERE ID_Estoque = ?', 'i', [$id])) {
        $msg_erro = "Não foi possível excluir: este registro possui dependências. Detalhe: " . database_error($con);
    } else {
        $msg = "Registro excluído com sucesso!";
    }

    if (empty($msg_erro)) {
        header("Location: estoque.php?ok=1");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_estoque'])) {
    $id = intval($_POST['id_estoque']);
    [$tipo, $qtd, $produto_id] = inventory_movement($con);
    
    if (inventory_execute($con, 'UPDATE tb_estoque SET Est_Tipo=?, Est_Quantidade=?, TB_Produtos_ID_Produto=? WHERE ID_Estoque=?', 'siii', [$tipo, $qtd, $produto_id, $id])) {
        header("Location: estoque.php?ok=update");
        exit();
    } else {
        $msg_erro = "Erro ao atualizar: " . database_error($con);
    }
}

if (isset($_GET['ok'])) {
    if ($_GET['ok'] == 'update') $msg = "Estoque atualizado com sucesso!";
    if ($_GET['ok'] == '1')      $msg = "Registro excluído com sucesso!";
}


} catch (AdminFormValidation $error) { $msg_erro = $error->getMessage(); http_response_code(400); }
$edit_estoque = null;
if (isset($_GET['acao']) && $_GET['acao'] == 'editar' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $edit_estoque = dashboard_query($con, 'SELECT * FROM tb_estoque WHERE ID_Estoque = ?', 'i', [$id])[0] ?? null;
}

include('header.php');
ui_render_page($con, 'estoque', false, $edit_estoque, $msg, $msg_erro);
include('footer.php');
