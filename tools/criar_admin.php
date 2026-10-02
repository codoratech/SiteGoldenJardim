<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../conexao/banco.php';
$count_stmt = mysqli_prepare($con, 'SELECT COUNT(*) FROM tb_login');
mysqli_stmt_execute($count_stmt);
if ((int) mysqli_fetch_row(mysqli_stmt_get_result($count_stmt))[0] !== 0) {
    exit("Já existem usuários. Use o painel para gerenciar acessos.\n");
}
$login = trim(readline('Login do administrador: '));
$nome = trim(readline('Nome: '));
$senha = readline('Senha (mínimo 8 caracteres; entrada visível neste terminal): ');
if (!$login || !$nome || strlen($senha) < 8 || strlen($login) > 50 || strlen($nome) > 120) exit("Dados inválidos.\n");
$hash = password_hash($senha, PASSWORD_DEFAULT);
$stmt = mysqli_prepare($con, "INSERT INTO tb_login (log_nome, log_login, log_senha, log_perfil, log_ativo) VALUES (?, ?, ?, 'Administrador', 1)");
mysqli_stmt_bind_param($stmt, 'sss', $nome, $login, $hash);
if (!mysqli_stmt_execute($stmt)) exit("Não foi possível criar o administrador.\n");
echo "Administrador criado.\n";
