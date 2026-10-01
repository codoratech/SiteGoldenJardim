<?php
require_once __DIR__ . "/bootstrap.php";
require_login();
require_once __DIR__ . '/ui_helpers.php';
include('../conexao/banco.php');
$msg = ''; $msg_erro = '';
try {
if (!empty($GLOBALS['admin_form_errors'])) throw new AdminFormValidation(implode(' ', $GLOBALS['admin_form_errors']));
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && ($_POST['acao'] ?? '') !== 'excluir') {
    foreach (['log_nome', 'log_login'] as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') reject_request('Preencha todos os campos obrigatórios.');
    }
}



$msg = "";
$msg_erro = "";

function is_protected_login($login) {
    return strcasecmp(trim((string) $login), 'Adm') === 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'excluir' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    if ($id < 1) reject_request('Registro inválido.');
    $target_res = ui_query($con, "SELECT log_login FROM tb_login WHERE log_codigo = ?", [$id]);
    $target = mysqli_fetch_assoc($target_res);
    $count_res = ui_query($con, "SELECT COUNT(*) as total FROM tb_login", []);
    $count_row = mysqli_fetch_assoc($count_res);
    if ($target && is_protected_login($target['log_login'])) {
        $msg_erro = 'O login Adm é protegido e não pode ser excluído.';
    } elseif ($id === (int) $_SESSION['log_codigo']) {
        $msg_erro = 'Não é possível excluir seu próprio acesso enquanto estiver conectado.';
    } elseif ($count_row['total'] <= 1) {
        $msg_erro = "Não é possível excluir o único usuário do sistema.";
    } else {
        if (!ui_query($con, "DELETE FROM tb_login WHERE log_codigo = ? AND LOWER(TRIM(log_login)) <> 'adm'", [$id])) {
            $msg_erro = "Erro ao excluir login: " . database_error($con);
        } else {
            $msg = "Login excluído com sucesso!";
        }
    }

    if (empty($msg_erro)) {
        header("Location: logins.php?ok=1");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_login'])) {
    $id = intval($_POST['id_login']);
    $target_res = ui_query($con, "SELECT log_login FROM tb_login WHERE log_codigo = ?", [$id]);
    $target = mysqli_fetch_assoc($target_res);
    if ($target && is_protected_login($target['log_login']) && $_POST['log_login'] !== $target['log_login']) {
        reject_request('O nome de login Adm é protegido e não pode ser alterado.');
    }
    $nome = $_POST['log_nome'];
    $login = $_POST['log_login'];
    $senha = $_POST['log_senha'];
    if (trim($_POST['log_nome']) === '' || trim($_POST['log_login']) === '') reject_request('Nome e login são obrigatórios.');
    if (strlen($_POST['log_nome']) > 120 || strlen($_POST['log_login']) > 50) reject_request('Nome ou login excede o tamanho permitido.');
    $duplicate = mysqli_prepare($con, 'SELECT log_codigo FROM tb_login WHERE log_login = ? AND log_codigo <> ?');
    $exclude_id = $id;
    mysqli_stmt_bind_param($duplicate, 'si', $_POST['log_login'], $exclude_id);
    mysqli_stmt_execute($duplicate);
    if (mysqli_num_rows(mysqli_stmt_get_result($duplicate)) > 0) reject_request('Este login já está em uso.');
    
    if (!empty($senha)) {
        if (strlen($senha) < 8) reject_request('A senha deve ter pelo menos 8 caracteres.');
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "UPDATE tb_login SET log_nome=?, log_login=?, log_senha=? WHERE log_codigo=?";
    $sql_params = [$nome, $login, $senha_hash, $id];
    } else {
        $sql = "UPDATE tb_login SET log_nome=?, log_login=? WHERE log_codigo=?";
    $sql_params = [$nome, $login, $id];
    }
    if (ui_query($con, $sql, $sql_params)) {
        header("Location: logins.php?ok=update");
        exit();
    } else {
        $msg_erro = "Erro ao atualizar login: " . database_error($con);
    }
}

if (isset($_GET['ok'])) {
    if ($_GET['ok'] == 'update') $msg = "Login atualizado com sucesso!";
    if ($_GET['ok'] == '1')      $msg = "Login excluído com sucesso!";
}


} catch (AdminFormValidation $error) { $msg_erro = $error->getMessage(); http_response_code(400); }
$edit_login = null;
if (isset($_GET['acao']) && $_GET['acao'] == 'editar' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = ui_query($con, "SELECT * FROM tb_login WHERE log_codigo = ?", [$id]);
    $edit_login = mysqli_fetch_assoc($res);
}

include('header.php');
ui_render_page($con, 'logins', false, $edit_login, $msg, $msg_erro);
include('footer.php');
