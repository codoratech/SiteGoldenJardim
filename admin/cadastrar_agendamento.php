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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cli_id = intval($_POST['TB_Clientes_ID_Cliente']);
    $os_id = !empty($_POST['TB_OrdensServico_ID_Ordem']) ? intval($_POST['TB_OrdensServico_ID_Ordem']) : null;
    $data = $_POST['Age_DataAgendada'];
    $status = $_POST['Age_Status'];
    $obs = $_POST['Age_Observacoes'];
    
    if (empty($cli_id) || empty($data)) {
        $msg_erro = "Cliente e Data Agendada são obrigatórios.";
    } else {
        if (ui_query($con, "INSERT INTO tb_agendamentos (TB_Clientes_ID_Cliente, TB_OrdensServico_ID_Ordem, Age_DataAgendada, Age_Status, Age_Observacoes) VALUES (?, ?, ?, ?, ?)", [$cli_id, $os_id, $data, $status, $obs])) {
            header("Location: cadastrar_agendamento.php?ok=insert");
            exit();
        } else {
            $msg_erro = "Erro ao cadastrar: " . database_error($con);
        }
    }
}

if (isset($_GET['ok']) && $_GET['ok'] == 'insert') {
    $msg = "Agendamento criado com sucesso!";
}

} catch (AdminFormValidation $error) { $msg_erro = $error->getMessage(); http_response_code(400); }

include('header.php');
ui_render_page($con, 'agendamentos', true, null, $msg, $msg_erro);
include('footer.php');
