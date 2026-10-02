<?php
require_once __DIR__ . '/bootstrap.php';
require_login();
require_once __DIR__ . '/ui_helpers.php';
require_once __DIR__ . '/login_access_helpers.php';
require __DIR__ . '/../conexao/banco.php';
$msg = ''; $msg_erro = ''; $transaction = false;
try {
    if (!empty($GLOBALS['admin_form_errors'])) throw new AdminFormValidation(implode(' ', $GLOBALS['admin_form_errors']));
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
        $deleting = ($_POST['acao'] ?? '') === 'excluir';
        $id = (int)($_POST[$deleting ? 'id' : 'id_login'] ?? 0);
        if ($id < 1) throw new AdminFormValidation('Registro inválido.');
        $nome = trim($_POST['log_nome'] ?? '');
        $login = trim($_POST['log_login'] ?? '');
        $senha = $_POST['log_senha'] ?? '';
        $perfil = $_POST['log_perfil'] ?? '';
        if (!$deleting) {
            if ($nome === '' || $login === '') throw new AdminFormValidation('Nome e login são obrigatórios.');
            if (strlen($nome) > 120 || strlen($login) > 50) throw new AdminFormValidation('Nome ou login excede o tamanho permitido.');
            if ($senha !== '' && (strlen($senha) < 8 || strlen($senha) > 72 || strpos($senha, "\0") !== false)) throw new AdminFormValidation('A senha deve ter entre 8 e 72 bytes.');
        }
        if (!mysqli_begin_transaction($con)) throw new RuntimeException('Falha ao iniciar transação.');
        $transaction = true;
        $records = ui_rows($con, 'SELECT log_codigo, log_login, log_perfil, log_ativo FROM tb_login ORDER BY log_codigo FOR UPDATE');
        admin_validate_login_change($records, $id, (int)$_SESSION['log_codigo'], $deleting, $login, $perfil);
        if ($deleting) {
            $saved = ui_query($con, 'DELETE FROM tb_login WHERE log_codigo = ?', [$id]);
        } else {
            $duplicate = ui_rows($con, 'SELECT log_codigo FROM tb_login WHERE log_login = ? AND log_codigo <> ?', [$login, $id]);
            if ($duplicate) throw new AdminFormValidation('Este login já está em uso.');
            if ($senha !== '') {
                $saved = ui_query($con, 'UPDATE tb_login SET log_nome=?, log_login=?, log_senha=?, log_perfil=? WHERE log_codigo=?', [$nome, $login, password_hash($senha, PASSWORD_DEFAULT), $perfil, $id]);
            } else {
                $saved = ui_query($con, 'UPDATE tb_login SET log_nome=?, log_login=?, log_perfil=? WHERE log_codigo=?', [$nome, $login, $perfil, $id]);
            }
        }
        if (!$saved || !mysqli_commit($con)) throw new RuntimeException('Falha ao salvar acesso.');
        $transaction = false;
        header('Location: logins.php?ok='.($deleting ? '1' : 'update'));
        exit;
    }
    if (($_GET['ok'] ?? '') === 'update') $msg = 'Login atualizado com sucesso!';
    if (($_GET['ok'] ?? '') === '1') $msg = 'Login excluído com sucesso!';
} catch (AdminFormValidation $error) {
    if ($transaction) mysqli_rollback($con);
    $msg_erro = $error->getMessage(); http_response_code(400);
} catch (Throwable $error) {
    if ($transaction) mysqli_rollback($con);
    error_log('Golden Jardim: falha ao salvar acesso.');
    $msg_erro = 'Não foi possível salvar a alteração. Tente novamente.'; http_response_code(503);
}
$edit_login = null;
if (($_GET['acao'] ?? '') === 'editar' && isset($_GET['id'])) {
    $res = ui_query($con, 'SELECT * FROM tb_login WHERE log_codigo = ?', [(int)$_GET['id']]);
    if ($res) $edit_login = mysqli_fetch_assoc($res);
}
include __DIR__ . '/header.php';
ui_render_page($con, 'logins', false, $edit_login, $msg, $msg_erro);
include __DIR__ . '/footer.php';
