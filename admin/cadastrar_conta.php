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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cli_id = intval($_POST['TB_Clientes_ID_Cliente']);
    $os_id = !empty($_POST['TB_OrdensServico_ID_Ordem']) ? intval($_POST['TB_OrdensServico_ID_Ordem']) : null;
    $valor = max(0, floatval(str_replace(',', '.', str_replace(['R$', ' '], '', $_POST['Con_Valor']))));
    $vencimento = $_POST['Con_DataVencimento'];
    $status = $_POST['Con_Status'];
    $forma = $_POST['Con_FormaPagamento'];
    
    if (empty($cli_id) || empty($vencimento)) {
        $msg_erro = "Cliente e Data de Vencimento são obrigatórios.";
    } else {
        if (ui_query($con, "INSERT INTO tb_contasreceber (TB_Clientes_ID_Cliente, TB_OrdensServico_ID_Ordem, Con_Valor, Con_DataVencimento, Con_Status, Con_FormaPagamento) VALUES (?, ?, ?, ?, ?, ?)", [$cli_id, $os_id, $valor, $vencimento, $status, $forma])) {
            header("Location: cadastrar_conta.php?ok=insert");
            exit();
        } else {
            $msg_erro = "Erro ao cadastrar: " . database_error($con);
        }
    }
}

if (isset($_GET['ok']) && $_GET['ok'] == 'insert') {
    $msg = "Conta a receber cadastrada com sucesso!";
}

} catch (AdminFormValidation $error) { $msg_erro = $error->getMessage(); http_response_code(400); }

include('header.php');
ui_render_page($con, 'financeiro', true, null, $msg, $msg_erro);
include('footer.php');
