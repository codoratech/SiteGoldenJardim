<?php
require_once __DIR__ . "/bootstrap.php";
require_login();
require_once __DIR__ . '/ui_helpers.php';
include('../conexao/banco.php');
$msg = ''; $msg_erro = '';
try {
if (!empty($GLOBALS['admin_form_errors'])) throw new AdminFormValidation(implode(' ', $GLOBALS['admin_form_errors']));
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && ($_POST['acao'] ?? '') !== 'excluir') {
    foreach (['cli_Nome'] as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') reject_request('Preencha todos os campos obrigatórios.');
    }
}



$msg = "";
$msg_erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'excluir' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    if ($id < 1) reject_request('Registro inválido.');
    if (!ui_query($con, "DELETE FROM tb_clientes WHERE ID_Cliente = ?", [$id])) {
        $msg_erro = "Não foi possível excluir: este cliente possui registros vinculados. Detalhe: " . database_error($con);
    } else {
        $msg = "Cliente excluído com sucesso!";
    }
    if (empty($msg_erro)) {
        header("Location: clientes.php?ok=1");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_cliente'])) {
    $id = intval($_POST['id_cliente']);
    $nome     = $_POST['cli_Nome'];
    $tipo     = $_POST['cli_Tipo'];
    $telefone = $_POST['cli_Telefone'];
    $email    = $_POST['cli_Email'];
    $endereco = $_POST['cli_Endereco'];

    $sql = "UPDATE tb_clientes\n            SET cli_Nome=?, cli_Tipo=?, cli_Telefone=?,\n                cli_Email=?, cli_Endereco=?\n            WHERE ID_Cliente=?";
    $sql_params = [$nome, $tipo, $telefone, $email, $endereco, $id];

    if (ui_query($con, $sql, $sql_params)) {
        header("Location: clientes.php?ok=update");
        exit();
    } else {
        $msg_erro = "Erro ao atualizar: " . database_error($con);
    }
}

if (isset($_GET['ok'])) {
    if ($_GET['ok'] == 'update') $msg = "Cliente atualizado com sucesso!";
    if ($_GET['ok'] == '1')      $msg = "Cliente excluído com sucesso!";
}


} catch (AdminFormValidation $error) { $msg_erro = $error->getMessage(); http_response_code(400); }
$edit_cliente = null;
if (isset($_GET['acao']) && $_GET['acao'] == 'editar' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = ui_query($con, "SELECT * FROM tb_clientes WHERE ID_Cliente = ?", [$id]);
    $edit_cliente = mysqli_fetch_assoc($res);
}

include('header.php');
ui_render_page($con, 'clientes', false, $edit_cliente, $msg, $msg_erro);
include('footer.php');
