<?php
require_once __DIR__ . '/ui_config.php';
require_once __DIR__ . '/icons.php';
require_once __DIR__ . '/dashboard_data.php';
function ui_escape($value) { return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function ui_query($con, $sql, $params = []) {
    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) return false;
    if ($params) {
        $types = '';
        foreach ($params as $param) $types .= is_int($param) ? 'i' : (is_float($param) ? 'd' : 's');
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    if (!mysqli_stmt_execute($stmt)) { mysqli_stmt_close($stmt); return false; }
    $result = mysqli_stmt_field_count($stmt) ? mysqli_stmt_get_result($stmt) : true;
    mysqli_stmt_close($stmt);
    return $result;
}
function ui_rows($con, $sql, $params = []) {
    $result = ui_query($con, $sql, $params);
    if ($result === false) throw new RuntimeException('Não foi possível carregar os registros.');
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
function ui_client_type($value) {
    $text = mb_strtolower(trim((string)$value), 'UTF-8');
    if (strpos($text, 'condom') === 0) return 'Condomínio';
    if ($text === 'residencial') return 'Residencial';
    if ($text === 'comercial') return 'Comercial';
    return trim((string)$value);
}
function ui_badge($value) {
    $value = ui_client_type($value);
    $classes = ['Pago'=>'success','Concluído'=>'success','Concluída'=>'success','Realizado'=>'success','Residencial'=>'success','Entrada'=>'success','Agendado'=>'info','Comercial'=>'info','Em Andamento'=>'info','Pendente'=>'warning','Condomínio'=>'warning','Saida'=>'warning','Vencido'=>'danger','Cancelado'=>'neutral','Crítico'=>'danger','Baixo'=>'warning'];
    return '<span class="status-badge '.($classes[$value] ?? 'neutral').'">'.ui_escape($value === 'Saida' ? 'Saída' : ($value ?: '—')).'</span>';
}
function ui_list_data($con, $entity) {
    $q = is_string($_GET['q'] ?? '') ? mb_substr(trim($_GET['q'] ?? ''),0,120) : '';
    $filter = is_string($_GET['filtro'] ?? '') ? ($_GET['filtro'] ?? '') : '';
    $where = []; $params = [];
    if ($q !== '') {
        $where[] = '(' . implode(' OR ',array_map(function($column){return "$column LIKE ?";},$entity['search'])) . ')';
        foreach ($entity['search'] as $_) $params[] = '%'.$q.'%';
    }
    if ($filter !== '' && in_array($filter,$entity['filters'] ?? [],true)) {
        if ($filter === 'Vencido') { $where[] = "cr.Con_Status = 'Pendente' AND cr.Con_DataVencimento < ?"; $params[] = ui_today(); }
        elseif ($entity['table'] === 'tb_clientes' && $filter === 'Condomínio') $where[] = "cli_Tipo LIKE 'Condom%'";
        else { $where[] = $entity['filter'].' = ?'; $params[] = $filter; }
    } else $filter = '';
    $sql = $entity['select'] . ($where ? ' WHERE '.implode(' AND ',$where) : '');
    $total = (int)ui_rows($con,'SELECT COUNT(*) AS total FROM ('.$sql.') AS filtered_rows',$params)[0]['total'];
    $all = (int)ui_rows($con,'SELECT COUNT(*) AS total FROM ('.$entity['select'].') AS all_rows')[0]['total'];
    $pages = max(1,(int)ceil($total / 10));
    $page = min($pages,max(1,(int)($_GET['pagina'] ?? 1)));
    $rows = ui_rows($con,$sql.' ORDER BY '.$entity['order'].' LIMIT ? OFFSET ?',array_merge($params,[10,($page-1)*10]));
    return compact('rows','total','all','pages','page','q','filter');
}
function ui_link($params = []) {
    return basename($_SERVER['PHP_SELF']).($params ? '?'.http_build_query($params) : '');
}
function ui_today() { return (new DateTimeImmutable('today',new DateTimeZone('America/Sao_Paulo')))->format('Y-m-d'); }
function ui_value($name, $record = []) {
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && ($_POST['acao'] ?? '') !== 'excluir' && $name !== 'log_senha') return $_POST[$name] ?? '';
    return $name === 'log_senha' ? '' : ($record[$name] ?? '');
}
function ui_field_error($field, $value, $required, $error) {
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST' || !$error || ($_POST['acao'] ?? '') === 'excluir') return '';
    $text = trim((string)$value);
    if ($required && $text === '') return 'Preencha este campo.';
    if ($field[2] === 'email' && $text !== '' && !filter_var($text,FILTER_VALIDATE_EMAIL)) return 'Informe um e-mail válido.';
    if ($field[2] === 'number' && $text !== '' && (!ctype_digit($text) || (float)$text > 2147483647)) return 'Informe um número inteiro válido.';
    if ($field[2] === 'password' && $text !== '' && strlen($text)<8) return 'Use pelo menos 8 caracteres.';
    if ($field[0] === 'log_login' && strpos($error,'login já')!==false) return $error;
    return '';
}
function ui_render_page($con, $key, $isCreate, $record, $message, $error) {
    $entity = admin_entities()[$key];
    $data = $isCreate ? null : ui_list_data($con,$entity);
    $editing = !$isCreate && ($record || (isset($_POST[$entity['edit']]) && ($_POST['acao'] ?? '') !== 'excluir'));
    $record = $record ?? [];
    if ($editing && !$record) {
        $record = ui_rows($con,'SELECT * FROM '.$entity['table'].' WHERE '.$entity['pk'].' = ?',[(int)$_POST[$entity['edit']]])[0] ?? [];
        if (!$record) $editing = false;
    }
    include __DIR__ . '/partials/manage_page.php';
}
