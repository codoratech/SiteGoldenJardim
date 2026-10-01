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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    [$tipo, $qtd, $produto_id] = inventory_movement($con);
    
    if (empty($tipo)) {
        $msg_erro = "O tipo / item de estoque é obrigatório.";
    } else {
        if (inventory_execute($con, 'INSERT INTO tb_estoque (Est_Tipo, Est_Quantidade, TB_Produtos_ID_Produto) VALUES (?,?,?)', 'sii', [$tipo, $qtd, $produto_id])) {
            header("Location: cadastrar_estoque.php?ok=insert");
            exit();
        } else {
            $msg_erro = "Erro ao registrar: " . database_error($con);
        }
    }
}

if (isset($_GET['ok']) && $_GET['ok'] == 'insert') {
    $msg = "Movimentação de estoque registrada com sucesso!";
}


} catch (AdminFormValidation $error) { $msg_erro = $error->getMessage(); http_response_code(400); }

include('header.php');
ui_render_page($con, 'estoque', true, null, $msg, $msg_erro);
include('footer.php');
