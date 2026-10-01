<?php
require_once __DIR__ . "/bootstrap.php";
require_login();
require_once __DIR__ . '/ui_helpers.php';
include('../conexao/banco.php');
$msg = ''; $msg_erro = '';
try {
if (!empty($GLOBALS['admin_form_errors'])) throw new AdminFormValidation(implode(' ', $GLOBALS['admin_form_errors']));
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && ($_POST['acao'] ?? '') !== 'excluir') {
    foreach (['For_Nome'] as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') reject_request('Preencha todos os campos obrigatórios.');
    }
}



$msg = "";
$msg_erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'excluir' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    if ($id < 1) reject_request('Registro inválido.');
    if (!ui_query($con, "DELETE FROM tb_fornecedores WHERE ID_Fornecedor = ?", [$id])) {
        $msg_erro = "Não foi possível excluir: este fornecedor possui registros vinculados. Detalhe: " . database_error($con);
    } else {
        $msg = "Fornecedor excluído com sucesso!";
    }

    if (empty($msg_erro)) {
        header("Location: fornecedores.php?ok=1");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_fornecedor'])) {
    $id = intval($_POST['id_fornecedor']);
    $nome = $_POST['For_Nome'];
    $telefone = $_POST['For_Telefone'];
    $endereco = $_POST['For_Endereco'];
    
    if (ui_query($con, "UPDATE tb_fornecedores SET For_Nome=?, For_Telefone=?, For_Endereco=? WHERE ID_Fornecedor=?", [$nome, $telefone, $endereco, $id])) {
        header("Location: fornecedores.php?ok=update");
        exit();
    } else {
        $msg_erro = "Erro ao atualizar: " . database_error($con);
    }
}

if (isset($_GET['ok'])) {
    if ($_GET['ok'] == 'update') $msg = "Fornecedor atualizado com sucesso!";
    if ($_GET['ok'] == '1')      $msg = "Fornecedor excluído com sucesso!";
}


} catch (AdminFormValidation $error) { $msg_erro = $error->getMessage(); http_response_code(400); }
$edit_fornecedor = null;
if (isset($_GET['acao']) && $_GET['acao'] == 'editar' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = ui_query($con, "SELECT * FROM tb_fornecedores WHERE ID_Fornecedor = ?", [$id]);
    $edit_fornecedor = mysqli_fetch_assoc($res);
}

include('header.php');
ui_render_page($con, 'fornecedores', false, $edit_fornecedor, $msg, $msg_erro);
include('footer.php');
