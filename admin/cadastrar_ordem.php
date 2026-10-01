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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cli_id = intval($_POST['TB_Clientes_ID_Cliente']);
    $status = $_POST['Or_Status'];
    $valor = max(0, floatval(str_replace(',', '.', str_replace(['R$', ' '], '', $_POST['Ord_ValorTotal']))));
    $obs = $_POST['Ord_Observacoes'];
    
    if (empty($cli_id)) {
        $msg_erro = "O cliente é obrigatório.";
    } else {
        if (ui_query($con, "INSERT INTO tb_ordensservico (TB_Clientes_ID_Cliente, Or_Status, Ord_ValorTotal, Ord_Observacoes) VALUES (?, ?, ?, ?)", [$cli_id, $status, $valor, $obs])) {
            header("Location: cadastrar_ordem.php?ok=insert");
            exit();
        } else {
            $msg_erro = "Erro ao cadastrar: " . database_error($con);
        }
    }
}

if (isset($_GET['ok']) && $_GET['ok'] == 'insert') {
    $msg = "Ordem de Serviço criada com sucesso!";
}

} catch (AdminFormValidation $error) { $msg_erro = $error->getMessage(); http_response_code(400); }

include('header.php');
ui_render_page($con, 'ordens', true, null, $msg, $msg_erro);
include('footer.php');
