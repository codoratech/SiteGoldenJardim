<?php
require_once __DIR__ . '/bootstrap.php';
require_login();
require_once __DIR__ . '/../conexao/banco.php';
require_once __DIR__ . '/ui_helpers.php';

// A identidade vem exclusivamente da sessão, inclusive na atualização.
$profile_id = (int) $_SESSION['log_codigo'];
$result = ui_query($con, 'SELECT * FROM tb_login WHERE log_codigo = ?', [$profile_id]);
if (!$result) reject_request('Não foi possível carregar seu perfil. Tente novamente.', 503);
$profile = mysqli_fetch_assoc($result);
if (!$profile) reject_request('Seu acesso não está mais disponível.', 403);

$error = '';
$success = $_SESSION['profile_success'] ?? '';
unset($_SESSION['profile_success']);
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $current = $_POST['senha_atual'] ?? '';
    $new = $_POST['nova_senha'] ?? '';
    $confirmation = $_POST['confirmacao'] ?? '';
    if ($current === '' || $new === '' || $confirmation === '') {
        $error = 'Preencha todos os campos de senha.';
    } elseif (!password_verify($current, $profile['log_senha'])) {
        $error = 'A senha atual está incorreta.';
    } elseif (strlen($new) < 8 || strlen($new) > 72 || strpos($new, "\0") !== false) {
        $error = 'A nova senha deve ter entre 8 e 72 bytes e não pode conter caracteres nulos.';
    } elseif ($new !== $confirmation) {
        $error = 'A confirmação não corresponde à nova senha.';
    } else {
        try {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            // Evita sobrescrever uma alteração de senha feita durante esta requisição.
            $statement = mysqli_prepare($con, 'UPDATE tb_login SET log_senha = ? WHERE log_codigo = ? AND log_senha = ?');
            if (!$statement) throw new RuntimeException('Falha ao preparar atualização.');
            mysqli_stmt_bind_param($statement, 'sis', $hash, $profile_id, $profile['log_senha']);
            $updated = mysqli_stmt_execute($statement) && mysqli_stmt_affected_rows($statement) === 1;
            mysqli_stmt_close($statement);
            if (!$updated) throw new RuntimeException('Falha ao atualizar senha.');
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['profile_success'] = 'Senha alterada com sucesso!';
            header('Location: perfil.php');
            exit;
        } catch (Throwable $exception) {
            error_log('Golden Jardim: falha ao alterar a senha do perfil.');
            $error = 'Não foi possível alterar sua senha. Atualize a página e tente novamente.';
            http_response_code(503);
        }
    }
    if ($error !== '' && http_response_code() !== 503) http_response_code(400);
}

$access = admin_role();
$details = ['Nome' => $profile['log_nome'], 'Login' => $profile['log_login'], 'Perfil' => $access];
if (!empty($profile['log_data_cadastro'])) {
    $created = date_create($profile['log_data_cadastro']);
    $details['Data de criação'] = $created ? $created->format('d/m/Y H:i') : $profile['log_data_cadastro'];
}
include __DIR__ . '/header.php';
?>
<div class="manage-page">
 <div class="page-heading"><div><p class="eyebrow">MINHA CONTA</p><h1>Meu perfil</h1><p>Consulte seus dados e altere sua senha de acesso.</p></div></div>
 <nav class="page-breadcrumb" aria-label="Navegação estrutural"><a href="dashboard.php">Painel</a><span aria-hidden="true">/</span><span aria-current="page">Meu perfil</span></nav>
 <?php if ($error !== ''): ?><div class="feedback feedback-error" role="alert"><?= admin_icon('alert') ?><span><?= ui_escape($error) ?></span></div><?php endif; ?>
 <?php if ($success !== ''): ?><div class="feedback feedback-success" role="status"><?= admin_icon('check') ?><span><?= ui_escape($success) ?></span></div><?php endif; ?>
 <section class="manage-card form-card" aria-labelledby="profileDetailsTitle">
  <div class="card-heading"><div><h2 id="profileDetailsTitle">Dados do perfil</h2><p>Informações somente para consulta.</p></div><?= admin_icon('user') ?></div>
  <div class="manage-form"><fieldset><legend>Meu acesso</legend><dl class="form-grid">
   <?php foreach ($details as $label => $value): ?><div class="form-field"><dt class="field-help"><?= ui_escape($label) ?></dt><dd><?= ui_escape($value) ?></dd></div><?php endforeach; ?>
  </dl></fieldset></div>
 </section>
 <section class="manage-card form-card" aria-labelledby="profilePasswordTitle">
  <div class="card-heading"><div><h2 id="profilePasswordTitle">Alterar minha senha</h2><p>Confirme sua senha atual para salvar uma nova senha.</p></div><?= admin_icon('lock') ?></div>
  <form method="POST" action="perfil.php" class="manage-form">
   <?= csrf_field() ?>
   <fieldset><legend>Segurança</legend><div class="form-grid">
    <div class="form-field field-wide"><label for="senha_atual">Senha atual</label><input id="senha_atual" name="senha_atual" type="password" autocomplete="current-password" required></div>
    <div class="form-field"><label for="nova_senha">Nova senha</label><input id="nova_senha" name="nova_senha" type="password" autocomplete="new-password" minlength="8" maxlength="72" aria-describedby="passwordHelp" required><p class="field-help" id="passwordHelp">Use pelo menos 8 caracteres (máximo de 72 bytes).</p></div>
    <div class="form-field"><label for="confirmacao">Confirmar nova senha</label><input id="confirmacao" name="confirmacao" type="password" autocomplete="new-password" minlength="8" maxlength="72" required></div>
   </div></fieldset>
   <div class="form-actions"><button class="primary-action" type="submit"><?= admin_icon('check') ?>Alterar senha</button></div>
  </form>
 </section>
</div>
<?php include __DIR__ . '/footer.php'; ?>
