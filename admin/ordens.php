<?php
require_once __DIR__ . "/bootstrap.php";
require_login();
require_once __DIR__ . '/ui_helpers.php';
include('../conexao/banco.php');
$msg = ''; $msg_erro = '';
try {
if (!empty($GLOBALS['admin_form_errors'])) throw new AdminFormValidation(implode(' ', $GLOBALS['admin_form_errors']));
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && ($_POST['acao'] ?? '') !== 'excluir') {
    foreach (['TB_Clientes_ID_Cliente', 'Ord_ValorTotal'] as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') reject_request('Preencha todos os campos obrigatórios.');
    }
}



$msg = "";
$msg_erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'excluir' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    if ($id < 1) reject_request('Registro inválido.');
    if (!ui_query($con, "DELETE FROM tb_ordensservico WHERE ID_Ordem = ?", [$id])) {
        $msg_erro = "Não foi possível excluir: esta Ordem de Serviço possui registros vinculados. Detalhe: " . database_error($con);
    } else {
        $msg = "Ordem de Serviço excluída com sucesso!";
    }

    if (empty($msg_erro)) {
        header("Location: ordens.php?ok=1");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_ordem'])) {
    $id = intval($_POST['id_ordem']);
    $cli_id = intval($_POST['TB_Clientes_ID_Cliente']);
    $status = $_POST['Or_Status'];
    $valor = max(0, floatval(str_replace(',', '.', str_replace(['R$', ' '], '', $_POST['Ord_ValorTotal']))));
    $obs = $_POST['Ord_Observacoes'];
    
    if (ui_query($con, "UPDATE tb_ordensservico SET TB_Clientes_ID_Cliente=?, Or_Status=?, Ord_ValorTotal=?, Ord_Observacoes=? WHERE ID_Ordem=?", [$cli_id, $status, $valor, $obs, $id])) {
        header("Location: ordens.php?ok=update");
        exit();
    } else {
        $msg_erro = "Erro ao atualizar: " . database_error($con);
    }
}

if (isset($_GET['ok'])) {
    if ($_GET['ok'] == 'update') $msg = "Ordem de Serviço atualizada com sucesso!";
    if ($_GET['ok'] == '1')      $msg = "Ordem de Serviço excluída com sucesso!";
}


} catch (AdminFormValidation $error) { $msg_erro = $error->getMessage(); http_response_code(400); }
$edit_ordem = null;
if (isset($_GET['acao']) && $_GET['acao'] == 'editar' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = ui_query($con, "SELECT * FROM tb_ordensservico WHERE ID_Ordem = ?", [$id]);
    $edit_ordem = mysqli_fetch_assoc($res);
}


include('header.php');
ui_render_page($con, 'ordens', false, $edit_ordem, $msg, $msg_erro);
include('footer.php');
