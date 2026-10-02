<?php
// Shared session and request protections. Load before database access or output.
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params([
        'httponly' => true,
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'samesite' => 'Lax',
    ]);
    session_start();
}
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') . '">';
}
function reject_request($message, $status = 400) {
    if ($status === 400 && !empty($GLOBALS['admin_collect_errors'])) throw new AdminFormValidation($message);
    http_response_code($status);
    exit(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));
}
class AdminFormValidation extends RuntimeException {}
require_once __DIR__ . '/access.php';
function require_login() {
    if (empty($_SESSION['log_codigo'])) {
        header('Location: index.php');
        exit;
    }
    admin_require_access();
}
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
        reject_request('A sessão do formulário expirou. Volte, atualize a página e tente novamente.', 403);
    }
    foreach ($_POST as $value) {
        if (!is_string($value)) reject_request('Dados de formulário inválidos.');
    }
    $form_routes = ['clientes','produtos','servicos','fornecedores','ordens','agendamentos','estoque','financeiro','logins','cadastrar_cliente','cadastrar_produto','cadastrar_servico','cadastrar_fornecedor','cadastrar_ordem','cadastrar_agendamento','cadastrar_estoque','cadastrar_conta','cadastrar_login'];
    $GLOBALS['admin_collect_errors'] = in_array(pathinfo($_SERVER['PHP_SELF'] ?? '',PATHINFO_FILENAME),$form_routes,true);
    try {
    if (!empty($_POST['cli_Email']) && !filter_var($_POST['cli_Email'], FILTER_VALIDATE_EMAIL)) {
        reject_request('Informe um endereço de e-mail válido.');
    }
    foreach ($_POST as $key => $value) {
        if (($key === 'id' || strpos($key, 'id_') === 0 || strpos($key, '_ID_') !== false) && $value !== '') {
            if (!ctype_digit($value) || (int) $value < 1) reject_request('Selecione um registro válido.');
        }
        if ($key === 'Est_Quantidade' && !ctype_digit($value)) reject_request('A quantidade deve ser um número inteiro positivo ou zero.');
        if (preg_match('/(?:Preco|Valor|Quantidade)/i', $key) && $value !== '') {
            $number = str_replace(['R$', ' ', ','], ['', '', '.'], $value);
            if (!is_numeric($number) || !is_finite((float) $number) || (float) $number < 0) {
                reject_request('Informe valores e quantidades válidos, maiores ou iguais a zero.');
            }
        }
        if (in_array($key, ['Con_DataVencimento', 'Age_DataAgendada'], true) && $value !== '') {
            $format = $key === 'Age_DataAgendada' ? 'Y-m-d\TH:i' : 'Y-m-d';
            $date = DateTime::createFromFormat('!' . $format, $value);
            if (!$date || $date->format($format) !== $value) reject_request('Informe uma data válida.');
        }
    }
    $choices = [
        'cli_Tipo' => ['Residencial', 'Condomínio', 'Comercial'],
        'Age_Status' => ['Agendado', 'Realizado', 'Cancelado'],
        'Or_Status' => ['Em Andamento', 'Concluído', 'Cancelado'],
        'Con_Status' => ['Pendente', 'Pago', 'Cancelado'],
    ];
    foreach ($choices as $key => $values) {
        if (isset($_POST[$key]) && !in_array($_POST[$key], $values, true)) reject_request('Selecione uma opção válida.');
    }
    } catch (AdminFormValidation $error) {
        $GLOBALS['admin_form_errors'][] = $error->getMessage();
        http_response_code(400);
    }
}
