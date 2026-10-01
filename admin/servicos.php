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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'excluir' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    if ($id < 1) reject_request('Registro inválido.');
    if (!ui_query($con, "DELETE FROM tb_servicos WHERE ID_Servico = ?", [$id])) {
        $msg_erro = "Não foi possível excluir: este serviço possui registros vinculados. Detalhe: " . database_error($con);
    } else {
        $msg = "Serviço excluído com sucesso!";
    }

    if (empty($msg_erro)) {
        header("Location: servicos.php?ok=1");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_servico'])) {
    $id = intval($_POST['id_servico']);
    $nome = $_POST['Ser_Nome'];
    $desc = $_POST['Ser_Descricao'];
    $preco = max(0, floatval(str_replace(',', '.', str_replace(['R$', ' '], '', $_POST['Ser_Preco']))));
    
    if (ui_query($con, "UPDATE tb_servicos SET Ser_Nome=?, Ser_Descricao=?, Ser_Preco=? WHERE ID_Servico=?", [$nome, $desc, $preco, $id])) {
        header("Location: servicos.php?ok=update");
        exit();
    } else {
        $msg_erro = "Erro ao atualizar: " . database_error($con);
    }
}

if (isset($_GET['ok'])) {
    if ($_GET['ok'] == 'update') $msg = "Serviço atualizado com sucesso!";
    if ($_GET['ok'] == '1')      $msg = "Serviço excluído com sucesso!";
}


} catch (AdminFormValidation $error) { $msg_erro = $error->getMessage(); http_response_code(400); }
$edit_servico = null;
if (isset($_GET['acao']) && $_GET['acao'] == 'editar' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = ui_query($con, "SELECT * FROM tb_servicos WHERE ID_Servico = ?", [$id]);
    $edit_servico = mysqli_fetch_assoc($res);
}

include('header.php');
ui_render_page($con, 'servicos', false, $edit_servico, $msg, $msg_erro);
include('footer.php');
