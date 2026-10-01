<?php
require_once __DIR__ . "/bootstrap.php";
require_login();
require_once __DIR__ . '/ui_helpers.php';
include('../conexao/banco.php');
$msg = ''; $msg_erro = '';
try {
if (!empty($GLOBALS['admin_form_errors'])) throw new AdminFormValidation(implode(' ', $GLOBALS['admin_form_errors']));
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && ($_POST['acao'] ?? '') !== 'excluir') {
    foreach (['Ser_Nome', 'Ser_Preco'] as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') reject_request('Preencha todos os campos obrigatórios.');
    }
}



$msg = "";
$msg_erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['Ser_Nome'];
    $desc = $_POST['Ser_Descricao'];
    $preco = max(0, floatval(str_replace(',', '.', str_replace(['R$', ' '], '', $_POST['Ser_Preco']))));
    
    if (empty($nome)) {
        $msg_erro = "O nome do serviço é obrigatório.";
    } else {
        if (ui_query($con, "INSERT INTO tb_servicos (Ser_Nome, Ser_Descricao, Ser_Preco) VALUES (?, ?, ?)", [$nome, $desc, $preco])) {
            header("Location: cadastrar_servico.php?ok=insert");
            exit();
        } else {
            $msg_erro = "Erro ao cadastrar: " . database_error($con);
        }
    }
}

if (isset($_GET['ok']) && $_GET['ok'] == 'insert') {
    $msg = "Serviço cadastrado com sucesso!";
}


} catch (AdminFormValidation $error) { $msg_erro = $error->getMessage(); http_response_code(400); }

include('header.php');
ui_render_page($con, 'servicos', true, null, $msg, $msg_erro);
include('footer.php');
