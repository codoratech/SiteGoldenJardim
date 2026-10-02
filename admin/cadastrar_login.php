<?php
require_once __DIR__ . "/bootstrap.php";
require_login();
require_once __DIR__ . '/ui_helpers.php';
include('../conexao/banco.php');
$msg = ''; $msg_erro = '';
try {
if (!empty($GLOBALS['admin_form_errors'])) throw new AdminFormValidation(implode(' ', $GLOBALS['admin_form_errors']));
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && ($_POST['acao'] ?? '') !== 'excluir') {
    foreach (['log_nome', 'log_login', 'log_senha'] as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') reject_request('Preencha todos os campos obrigatórios.');
    }
}



$msg = "";
$msg_erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['log_nome'];
    $login = $_POST['log_login'];
    $senha = $_POST['log_senha'];
    $perfil = $_POST['log_perfil'] ?? '';
    if (!in_array($perfil, admin_roles(), true)) reject_request('Selecione um perfil de usuário válido.');
    if (strcasecmp(trim($login), 'Adm') === 0 && $perfil !== 'Administrador') reject_request('O login Adm deve permanecer Administrador.');
    if (trim($_POST['log_nome']) === '' || trim($_POST['log_login']) === '') reject_request('Nome e login são obrigatórios.');
    if (strlen($_POST['log_nome']) > 120 || strlen($_POST['log_login']) > 50) reject_request('Nome ou login excede o tamanho permitido.');
    $duplicate = mysqli_prepare($con, 'SELECT log_codigo FROM tb_login WHERE log_login = ? AND log_codigo <> ?');
    $exclude_id = 0;
    mysqli_stmt_bind_param($duplicate, 'si', $_POST['log_login'], $exclude_id);
    mysqli_stmt_execute($duplicate);
    if (mysqli_num_rows(mysqli_stmt_get_result($duplicate)) > 0) reject_request('Este login já está em uso.');
    
    if (empty($nome) || empty($login) || empty($senha)) {
        $msg_erro = "Todos os campos são obrigatórios.";
    } else {
        if (strlen($senha) < 8 || strlen($senha) > 72 || strpos($senha, "\0") !== false) reject_request('A senha deve ter entre 8 e 72 bytes.');
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        if (ui_query($con, "INSERT INTO tb_login (log_nome, log_login, log_senha, log_perfil) VALUES (?, ?, ?, ?)", [$nome, $login, $senha_hash, $perfil])) {
            header("Location: cadastrar_login.php?ok=insert");
            exit();
        } else {
            $msg_erro = "Erro ao cadastrar login: " . database_error($con);
        }
    }
}

if (isset($_GET['ok']) && $_GET['ok'] == 'insert') {
    $msg = "Login cadastrado com sucesso!";
}


} catch (AdminFormValidation $error) { $msg_erro = $error->getMessage(); http_response_code(400); }

include('header.php');
ui_render_page($con, 'logins', true, null, $msg, $msg_erro);
include('footer.php');
