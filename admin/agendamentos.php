<?php
require_once __DIR__ . "/bootstrap.php";
require_login();
require_once __DIR__ . '/ui_helpers.php';
include('../conexao/banco.php');
$msg = ''; $msg_erro = '';
try {
if (!empty($GLOBALS['admin_form_errors'])) throw new AdminFormValidation(implode(' ', $GLOBALS['admin_form_errors']));
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && ($_POST['acao'] ?? '') !== 'excluir') {
    foreach (['TB_Clientes_ID_Cliente', 'Age_DataAgendada'] as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') reject_request('Preencha todos os campos obrigatórios.');
    }
}



$msg = "";
$msg_erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'excluir' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    if ($id < 1) reject_request('Registro inválido.');
    if (!ui_query($con, "DELETE FROM tb_agendamentos WHERE ID_Agendamento = ?", [$id])) {
        $msg_erro = "Não foi possível excluir: este agendamento possui registros vinculados. Detalhe: " . database_error($con);
    } else {
        $msg = "Agendamento excluído com sucesso!";
    }

    if (empty($msg_erro)) {
        header("Location: agendamentos.php?ok=1");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_agendamento'])) {
    $id = intval($_POST['id_agendamento']);
    $cli_id = intval($_POST['TB_Clientes_ID_Cliente']);
    $os_id = !empty($_POST['TB_OrdensServico_ID_Ordem']) ? intval($_POST['TB_OrdensServico_ID_Ordem']) : null;
    $data = $_POST['Age_DataAgendada'];
    $status = $_POST['Age_Status'];
    $obs = $_POST['Age_Observacoes'];
    
    if (ui_query($con, "UPDATE tb_agendamentos SET TB_Clientes_ID_Cliente=?, TB_OrdensServico_ID_Ordem=?, Age_DataAgendada=?, Age_Status=?, Age_Observacoes=? WHERE ID_Agendamento=?", [$cli_id, $os_id, $data, $status, $obs, $id])) {
        header("Location: agendamentos.php?ok=update");
        exit();
    } else {
        $msg_erro = "Erro ao atualizar: " . database_error($con);
    }
}

if (isset($_GET['ok'])) {
    if ($_GET['ok'] == 'update') $msg = "Agendamento atualizado com sucesso!";
    if ($_GET['ok'] == '1')      $msg = "Agendamento excluído com sucesso!";
}


} catch (AdminFormValidation $error) { $msg_erro = $error->getMessage(); http_response_code(400); }
$edit_agendamento = null;
if (isset($_GET['acao']) && $_GET['acao'] == 'editar' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = ui_query($con, "SELECT * FROM tb_agendamentos WHERE ID_Agendamento = ?", [$id]);
    $edit_agendamento = mysqli_fetch_assoc($res);
}


include('header.php');
ui_render_page($con, 'agendamentos', false, $edit_agendamento, $msg, $msg_erro);
include('footer.php');
