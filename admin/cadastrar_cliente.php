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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome     = $_POST['cli_Nome'];
    $tipo     = $_POST['cli_Tipo'];
    $telefone = $_POST['cli_Telefone'];
    $email    = $_POST['cli_Email'];
    $endereco = $_POST['cli_Endereco'];

    if (empty($nome)) {
        $msg_erro = "O nome do cliente é obrigatório.";
    } else {
        $sql = "INSERT INTO tb_clientes (cli_Nome, cli_Tipo, cli_Telefone, cli_Email, cli_Endereco)\n                VALUES (?, ?, ?, ?, ?)";
    $sql_params = [$nome, $tipo, $telefone, $email, $endereco];

        if (ui_query($con, $sql, $sql_params)) {
            header("Location: cadastrar_cliente.php?ok=insert");
            exit();
        } else {
            $msg_erro = "Erro ao cadastrar: " . database_error($con);
        }
    }
}

if (isset($_GET['ok']) && $_GET['ok'] == 'insert') {
    $msg = "Cliente cadastrado com sucesso!";
}


} catch (AdminFormValidation $error) { $msg_erro = $error->getMessage(); http_response_code(400); }

include('header.php');
ui_render_page($con, 'clientes', true, null, $msg, $msg_erro);
include('footer.php');
