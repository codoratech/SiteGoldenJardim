<?php
require_once __DIR__ . "/bootstrap.php";
require_login();
require_once __DIR__ . '/ui_helpers.php';
include('../conexao/banco.php');
$msg = ''; $msg_erro = '';
try {
if (!empty($GLOBALS['admin_form_errors'])) throw new AdminFormValidation(implode(' ', $GLOBALS['admin_form_errors']));
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && ($_POST['acao'] ?? '') !== 'excluir') {
    foreach (['Pro_Nome', 'Pro_Preco'] as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') reject_request('Preencha todos os campos obrigatórios.');
    }
}


require_once __DIR__ . "/inventory_helpers.php";

$msg = "";
$msg_erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['Pro_Nome']);
    $desc = $_POST['Pro_Descricao'] ?? '';
    $minimum = inventory_minimum();
    $preco = max(0, floatval(str_replace(',', '.', str_replace(['R$', ' '], '', $_POST['Pro_Preco']))));
    
    if (empty($nome)) {
        $msg_erro = "O nome do produto é obrigatório.";
    } else {
        if (inventory_execute($con, 'INSERT INTO tb_produtos (Pro_Nome, Pro_Descricao, Pro_Preco, Pro_EstoqueMinimo) VALUES (?,?,?,?)', 'ssdi', [$nome, $desc, $preco, $minimum])) {
            header("Location: cadastrar_produto.php?ok=insert");
            exit();
        } else {
            $msg_erro = "Erro ao cadastrar: " . database_error($con);
        }
    }
}

if (isset($_GET['ok']) && $_GET['ok'] == 'insert') {
    $msg = "Produto cadastrado com sucesso!";
}


} catch (AdminFormValidation $error) { $msg_erro = $error->getMessage(); http_response_code(400); }

include('header.php');
ui_render_page($con, 'produtos', true, null, $msg, $msg_erro);
include('footer.php');
