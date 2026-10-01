<?php
function dashboard_query($con, $sql, $types = '', $params = []) {
    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) throw new RuntimeException(database_error($con));
    if ($types !== '') mysqli_stmt_bind_param($stmt, $types, ...$params);
    if (!mysqli_stmt_execute($stmt)) throw new RuntimeException(database_error($con));
    $rows = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $rows;
}
function dashboard_escape($value) { return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function dashboard_money($value) { return 'R$ ' . number_format((float) $value, 2, ',', '.'); }
function dashboard_stock($con) {
    $column = dashboard_query($con, "SELECT COUNT(*) AS total FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?", 'ss', ['tb_produtos', 'Pro_EstoqueMinimo']);
    $link = dashboard_query($con, "SELECT COUNT(*) AS total FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?", 'ss', ['tb_estoque', 'TB_Produtos_ID_Produto']);
    if (!$column[0]['total'] || !$link[0]['total']) return ['ready' => false, 'alerts' => [], 'critical' => 0];
    $rows = dashboard_query($con, "SELECT p.ID_Produto, p.Pro_Nome, p.Pro_EstoqueMinimo AS minimo, COALESCE(SUM(CASE WHEN LOWER(TRIM(e.Est_Tipo)) = 'entrada' THEN e.Est_Quantidade WHEN LOWER(TRIM(e.Est_Tipo)) IN ('saida', 'saída') THEN -e.Est_Quantidade ELSE 0 END), 0) AS saldo FROM tb_produtos p LEFT JOIN tb_estoque e ON e.TB_Produtos_ID_Produto = p.ID_Produto WHERE p.Pro_EstoqueMinimo IS NOT NULL GROUP BY p.ID_Produto, p.Pro_Nome, p.Pro_EstoqueMinimo HAVING saldo <= minimo ORDER BY saldo, p.Pro_Nome");
    $critical = 0;
    foreach ($rows as &$row) {
        $row['critical'] = (int) $row['saldo'] <= 0 || (int) $row['saldo'] <= (int) $row['minimo'] * .25;
        if ($row['critical']) $critical++;
        $row['progress'] = $row['minimo'] > 0 ? max(0, min(100, round(100 * $row['saldo'] / $row['minimo']))) : 0;
    }
    unset($row);
    return ['ready' => true, 'alerts' => $rows, 'critical' => $critical];
}
function dashboard_status($value) {
    $value = mb_strtolower(trim($value), 'UTF-8');
    if (strpos($value, 'concl') === 0) return 'Concluída';
    if (strpos($value, 'cancel') === 0) return 'Cancelada';
    if (strpos($value, 'andamento') !== false) return 'Em andamento';
    if ($value === 'pendente') return 'Pendente';
    return 'Outros';
}
function dashboard_data($con) {
    $now = new DateTimeImmutable('now', new DateTimeZone('America/Sao_Paulo'));
    $month = $now->modify('first day of this month')->setTime(0, 0);
    $previous = $month->modify('-1 month');
    $next = $month->modify('+1 month');
    $start = $month->modify('-5 months');
    $monthNames = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
    $months = []; $labels = [];
    for ($i = 0; $i < 6; $i++) {
        $date = $start->modify("+$i months");
        $months[] = $date->format('Y-m');
        $labels[] = $monthNames[(int) $date->format('n') - 1] . '/' . $date->format('y');
    }
    $counts = dashboard_query($con, "SELECT (SELECT COUNT(*) FROM tb_clientes) clientes, (SELECT COUNT(*) FROM tb_ordensservico) ordens, (SELECT COUNT(*) FROM tb_servicos) servicos, (SELECT COUNT(*) FROM tb_produtos) produtos, (SELECT COUNT(*) FROM tb_agendamentos) agendamentos, (SELECT COALESCE(SUM(Con_Valor), 0) FROM tb_contasreceber WHERE Con_Status = ?) pendente", 's', ['Pendente'])[0];
    $variations = [];
    // Identificadores fixos; apenas os limites de datas são parâmetros de entrada.
    foreach (['clientes' => ['tb_clientes', 'cli_DataCadastro'], 'ordens' => ['tb_ordensservico', 'Or_DataServico'], 'agendamentos' => ['tb_agendamentos', 'Age_DataAgendada']] as $key => $source) {
        [$table, $dateColumn] = $source;
        $row = dashboard_query($con, "SELECT COALESCE(SUM($dateColumn >= ? AND $dateColumn < ?),0) atual, COALESCE(SUM($dateColumn >= ? AND $dateColumn < ?),0) anterior FROM $table WHERE $dateColumn >= ? AND $dateColumn < ?", 'ssssss', [$month->format('Y-m-d'), $next->format('Y-m-d'), $previous->format('Y-m-d'), $month->format('Y-m-d'), $previous->format('Y-m-d'), $next->format('Y-m-d')])[0];
        $variations[$key] = $row['anterior'] > 0 ? sprintf('%+.0f%% vs. mês anterior', 100 * ($row['atual'] - $row['anterior']) / $row['anterior']) : ($row['atual'] > 0 ? $row['atual'] . ' neste mês · sem base anterior' : 'Sem novos registros neste mês');
    }
    $osRows = dashboard_query($con, "SELECT DATE_FORMAT(Or_DataServico, '%Y-%m') mes, COUNT(*) total FROM tb_ordensservico WHERE Or_DataServico >= ? AND Or_DataServico < ? GROUP BY mes ORDER BY mes", 'ss', [$start->format('Y-m-d'), $next->format('Y-m-d')]);
    $os = array_fill(0, 6, 0);
    foreach ($osRows as $row) { $index = array_search($row['mes'], $months, true); if ($index !== false) $os[$index] = (int) $row['total']; }
    $status = ['Pendente' => 0, 'Em andamento' => 0, 'Concluída' => 0, 'Cancelada' => 0, 'Outros' => 0];
    foreach (dashboard_query($con, 'SELECT Or_Status, COUNT(*) total FROM tb_ordensservico GROUP BY Or_Status') as $row) $status[dashboard_status($row['Or_Status'])] += (int) $row['total'];
    if (!$status['Outros']) unset($status['Outros']);
    $financeRows = dashboard_query($con, "SELECT DATE_FORMAT(Con_DataVencimento, '%Y-%m') mes, COALESCE(SUM(CASE WHEN Con_Status = ? THEN Con_Valor ELSE 0 END),0) recebido, COALESCE(SUM(CASE WHEN Con_Status = ? THEN Con_Valor ELSE 0 END),0) pendente FROM tb_contasreceber WHERE Con_DataVencimento >= ? AND Con_DataVencimento < ? GROUP BY mes ORDER BY mes", 'ssss', ['Pago', 'Pendente', $start->format('Y-m-d'), $next->format('Y-m-d')]);
    $received = $pending = array_fill(0, 6, 0);
    foreach ($financeRows as $row) { $index = array_search($row['mes'], $months, true); if ($index !== false) { $received[$index] = (float) $row['recebido']; $pending[$index] = (float) $row['pendente']; } }
    $top = dashboard_query($con, "SELECT s.Ser_Nome, SUM(i.Ite_Quantidade) total FROM tb_itensordemservico i JOIN tb_servicos s ON s.ID_Servico = i.TB_Servicos_ID_Servico JOIN tb_ordensservico o ON o.ID_Ordem = i.TB_OrdensServico_ID_Ordem WHERE o.Or_Status IN (?, ?, ?) GROUP BY s.ID_Servico, s.Ser_Nome HAVING total > 0 ORDER BY total DESC, s.Ser_Nome LIMIT 5", 'sss', ['Concluído', 'Concluída', 'Concluido']);
    $appointments = dashboard_query($con, "SELECT a.ID_Agendamento, a.Age_DataAgendada, a.Age_Status, c.cli_Nome, (SELECT GROUP_CONCAT(DISTINCT s.Ser_Nome ORDER BY s.Ser_Nome SEPARATOR ', ') FROM tb_itensordemservico i JOIN tb_servicos s ON s.ID_Servico = i.TB_Servicos_ID_Servico WHERE i.TB_OrdensServico_ID_Ordem = a.TB_OrdensServico_ID_Ordem) servico FROM tb_agendamentos a JOIN tb_clientes c ON c.ID_Cliente = a.TB_Clientes_ID_Cliente WHERE a.Age_DataAgendada >= ? AND a.Age_Status NOT IN (?, ?, ?) ORDER BY a.Age_DataAgendada, a.ID_Agendamento LIMIT 5", 'ssss', [$now->format('Y-m-d H:i:s'), 'Cancelado', 'Concluído', 'Concluido']);
    $recent = dashboard_query($con, 'SELECT o.ID_Ordem, o.Or_DataServico, o.Or_Status, o.Ord_ValorTotal, c.cli_Nome FROM tb_ordensservico o JOIN tb_clientes c ON c.ID_Cliente = o.TB_Clientes_ID_Cliente ORDER BY o.Or_DataServico DESC, o.ID_Ordem DESC LIMIT 5');
    $overdue = dashboard_query($con, 'SELECT COALESCE(SUM(Con_Valor),0) total FROM tb_contasreceber WHERE Con_Status = ? AND Con_DataVencimento < ?', 'ss', ['Pendente', $now->format('Y-m-d')])[0]['total'];
    return compact('counts', 'variations', 'labels', 'os', 'status', 'received', 'pending', 'top', 'appointments', 'recent', 'overdue');
}
