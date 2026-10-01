<?php
require_once __DIR__ . "/bootstrap.php";
require_login();
require_once __DIR__ . '/ui_helpers.php';
include('../conexao/banco.php');
$msg = ''; $msg_erro = '';
try {
if (!empty($GLOBALS['admin_form_errors'])) throw new AdminFormValidation(implode(' ', $GLOBALS['admin_form_errors']));
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && ($_POST['acao'] ?? '') !== 'excluir') {
    foreach (['TB_Clientes_ID_Cliente', 'Con_Valor', 'Con_DataVencimento'] as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') reject_request('Preencha todos os campos obrigatórios.');
    }
}



$msg = "";
$msg_erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'excluir' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    if ($id < 1) reject_request('Registro inválido.');
    if (!ui_query($con, "DELETE FROM tb_contasreceber WHERE ID_ContaReceber = ?", [$id])) {
        $msg_erro = "Não foi possível excluir: esta conta possui registros vinculados. Detalhe: " . database_error($con);
    } else {
        $msg = "Conta excluída com sucesso!";
    }

    if (empty($msg_erro)) {
        header("Location: financeiro.php?ok=1");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_conta'])) {
    $id = intval($_POST['id_conta']);
    $cli_id = intval($_POST['TB_Clientes_ID_Cliente']);
    $os_id = !empty($_POST['TB_OrdensServico_ID_Ordem']) ? intval($_POST['TB_OrdensServico_ID_Ordem']) : null;
    $valor = max(0, floatval(str_replace(',', '.', str_replace(['R$', ' '], '', $_POST['Con_Valor']))));
    $vencimento = $_POST['Con_DataVencimento'];
    $status = $_POST['Con_Status'];
    $forma = $_POST['Con_FormaPagamento'];
    
    if (ui_query($con, "UPDATE tb_contasreceber SET TB_Clientes_ID_Cliente=?, TB_OrdensServico_ID_Ordem=?, Con_Valor=?, Con_DataVencimento=?, Con_Status=?, Con_FormaPagamento=? WHERE ID_ContaReceber=?", [$cli_id, $os_id, $valor, $vencimento, $status, $forma, $id])) {
        header("Location: financeiro.php?ok=update");
        exit();
    } else {
        $msg_erro = "Erro ao atualizar: " . database_error($con);
    }
}

if (isset($_GET['ok'])) {
    if ($_GET['ok'] == 'update') $msg = "Conta a receber atualizada com sucesso!";
    if ($_GET['ok'] == '1')      $msg = "Conta excluída com sucesso!";
}


} catch (AdminFormValidation $error) { $msg_erro = $error->getMessage(); http_response_code(400); }
$edit_conta = null;
if (isset($_GET['acao']) && $_GET['acao'] == 'editar' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = ui_query($con, "SELECT * FROM tb_contasreceber WHERE ID_ContaReceber = ?", [$id]);
    $edit_conta = mysqli_fetch_assoc($res);
}


$contas = ui_query($con, "SELECT cr.*, c.cli_Nome FROM tb_contasreceber cr JOIN tb_clientes c ON cr.TB_Clientes_ID_Cliente = c.ID_Cliente ORDER BY cr.Con_DataVencimento DESC", []);


include('header.php');
ui_render_page($con, 'financeiro', false, $edit_conta, $msg, $msg_erro);
include('footer.php');
