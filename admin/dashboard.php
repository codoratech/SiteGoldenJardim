<?php
require_once __DIR__ . '/bootstrap.php';
require_login();
require __DIR__ . '/../conexao/banco.php';
require_once __DIR__ . '/dashboard_data.php';
require_once __DIR__ . '/icons.php';
try {
    $stats = dashboard_data($con);
    $admin_stock = dashboard_stock($con);
} catch (RuntimeException $error) {
    http_response_code(503);
    exit('Não foi possível carregar a visão geral. Tente novamente em alguns instantes.');
}
include __DIR__ . '/header.php';
$permission_message = $_SESSION['admin_permission_error'] ?? '';
unset($_SESSION['admin_permission_error']);
$cards = [
 ['clientes','Clientes','users','clientes.php','Total de clientes cadastrados'],
 ['ordens','Ordens de serviço','clipboard','ordens.php','Todas as ordens registradas'],
 ['pendente','Contas pendentes','wallet','financeiro.php','Total em aberto · todos os vencimentos'],
 ['servicos','Serviços cadastrados','leaf','servicos.php','Serviços disponíveis no catálogo'],
 ['produtos','Produtos','box','produtos.php','Produtos e insumos cadastrados'],
 ['agendamentos','Agendamentos','calendar','agendamentos.php','Todos os agendamentos registrados'],
];
$chartData = ['labels'=>$stats['labels'],'orders'=>$stats['os'],'statusLabels'=>array_keys($stats['status']),'statusValues'=>array_values($stats['status']),'received'=>$stats['received'],'pending'=>$stats['pending'],'serviceLabels'=>array_column($stats['top'],'Ser_Nome'),'serviceValues'=>array_map('intval',array_column($stats['top'],'total'))];
?>
<section class="dashboard">
 <?php if ($permission_message !== ''): ?><div class="critical-notice" role="alert"><?= admin_icon('lock') ?><span><?= dashboard_escape($permission_message) ?></span></div><?php endif; ?>
 <div class="dashboard-heading"><div><p class="eyebrow">GOLDEN JARDIM · GESTÃO</p><h1>Visão Geral<span>.</span></h1><p>Acompanhe o que importa e planeje os próximos passos.</p></div><?php if (admin_can_write()): ?><a class="primary-action" href="cadastrar_agendamento.php"><?= admin_icon('plus') ?> Novo agendamento</a><?php endif; ?></div>
 <?php if ($admin_stock['critical']): ?><a class="critical-notice" href="estoque.php"><?= admin_icon('alert') ?><span><strong><?= dashboard_escape($admin_stock['critical']) ?> <?= $admin_stock['critical'] === 1 ? 'produto com estoque crítico' : 'produtos com estoque crítico' ?></strong> · Confira os itens que precisam de reposição.</span><?= admin_icon('arrow') ?></a><?php endif; ?>
 <div class="kpi-grid">
 <?php foreach ($cards as [$key,$label,$icon,$url,$description]): ?>
 <a class="kpi-card" href="<?= dashboard_escape($url) ?>"><div class="kpi-top"><span class="kpi-icon"><?= admin_icon($icon) ?></span><span><?= dashboard_escape($label) ?></span><?= admin_icon('arrow','kpi-arrow') ?></div><strong class="kpi-value"><?= dashboard_escape($key === 'pendente' ? dashboard_money($stats['counts'][$key]) : number_format($stats['counts'][$key],0,',','.')) ?></strong><small><?= dashboard_escape($stats['variations'][$key] ?? $description) ?></small></a>
 <?php endforeach; ?>
 </div>
 <div class="section-heading"><h2>Panorama da operação</h2><span>Últimos 6 meses</span></div>
 <div class="charts-grid">
 <?php foreach ([['orders','Ordens por mês','Volume pela data do serviço'],['status','Distribuição das ordens','Todas as ordens · por status'],['finance','Contas a receber','Pagas x pendentes · por mês de vencimento'],['services','Serviços mais realizados','Top 5 · quantidades em ordens concluídas']] as [$id,$title,$subtitle]): ?>
 <article class="dashboard-panel chart-panel"><div class="panel-heading"><div><h3><?= dashboard_escape($title) ?></h3><p><?= dashboard_escape($subtitle) ?></p></div></div><div class="chart-frame" data-chart-frame="<?= dashboard_escape($id) ?>" aria-busy="true"><div class="chart-skeleton" role="status"><span class="sr-only">Carregando gráfico</span></div><canvas id="chart-<?= dashboard_escape($id) ?>" role="img" aria-label="<?= dashboard_escape($title) ?>"></canvas><p class="chart-empty" hidden></p></div><details class="chart-accessible"><summary>Ver dados em texto</summary><div id="chart-data-<?= dashboard_escape($id) ?>"></div></details></article>
 <?php endforeach; ?>
 </div>
 <div class="operations-grid">
  <article class="dashboard-panel"><div class="panel-heading"><div><h2>Próximos agendamentos</h2><p>Os próximos 5 compromissos</p></div><a href="agendamentos.php" class="subtle-link">Ver agenda <?= admin_icon('arrow') ?></a></div>
  <?php if (!$stats['appointments']): ?><div class="empty-state"><?= admin_icon('calendar') ?><strong>Agenda livre por enquanto</strong><p>Seus próximos compromissos aparecerão aqui.</p><?php if (admin_can_write()): ?><a href="cadastrar_agendamento.php">Agendar um serviço</a><?php endif; ?></div><?php else: ?><div class="appointment-list">
  <?php foreach ($stats['appointments'] as $item): $date = new DateTimeImmutable($item['Age_DataAgendada']); ?><a href="<?= admin_can_write() ? 'agendamentos.php?acao=editar&amp;id='.dashboard_escape($item['ID_Agendamento']) : 'agendamentos.php' ?>" class="appointment"><span class="date-tile"><strong><?= dashboard_escape($date->format('d')) ?></strong><small><?= dashboard_escape($date->format('m/Y')) ?></small></span><span class="appointment-info"><strong><?= dashboard_escape($item['cli_Nome']) ?></strong><small><?= dashboard_escape($item['servico'] ?: 'Serviço ainda não vinculado') ?></small></span><span class="appointment-time"><strong><?= dashboard_escape($date->format('H:i')) ?></strong><small><?= dashboard_escape($item['Age_Status']) ?></small></span></a><?php endforeach; ?></div><?php endif; ?>
  </article>
  <article class="dashboard-panel"><div class="panel-heading"><div><h2>Estoque em alerta</h2><p>Mínimos configurados por produto</p></div><span class="count-pill"><?= dashboard_escape(count($admin_stock['alerts'])) ?> alertas</span></div>
  <?php if (!$admin_stock['ready']): ?><div class="empty-state"><?= admin_icon('box') ?><strong>Configure o controle de estoque</strong><p>Aplique a migração de mínimos e vínculos para habilitar os alertas.</p></div><?php elseif (!$admin_stock['alerts']): ?><div class="empty-state"><?= admin_icon('box') ?><strong>Nenhum alerta de estoque</strong><p>Defina o mínimo nos produtos e registre entradas e saídas vinculadas.</p><a href="produtos.php">Configurar produtos</a></div><?php else: ?><div class="stock-alert-list">
  <?php foreach (array_slice($admin_stock['alerts'],0,5) as $alert): ?><div class="stock-alert"><div><strong><?= dashboard_escape($alert['Pro_Nome']) ?></strong><span class="status-badge <?= $alert['critical'] ? 'danger' : 'warning' ?>"><?= $alert['critical'] ? 'Crítico' : 'Baixo' ?></span></div><progress class="<?= $alert['critical'] ? 'danger' : 'warning' ?>" max="100" value="<?= dashboard_escape($alert['progress']) ?>" aria-label="<?= dashboard_escape('Nível de estoque de '.$alert['Pro_Nome']) ?>"></progress><small><?= dashboard_escape($alert['saldo']) ?> em estoque <span>Mínimo: <?= dashboard_escape($alert['minimo']) ?></span></small></div><?php endforeach; ?></div><?php endif; ?>
  <a href="estoque.php" class="panel-footer">Gerenciar estoque <?= admin_icon('arrow') ?></a></article>
 </div>
 <article class="dashboard-panel recent-orders"><div class="panel-heading"><div><h2>Ordens de serviço recentes</h2><p>As últimas 5 ordens registradas</p></div><a href="ordens.php" class="subtle-link">Ver todas <?= admin_icon('arrow') ?></a></div>
 <?php if (!$stats['recent']): ?><div class="empty-state"><?= admin_icon('clipboard') ?><strong>Nenhuma ordem por aqui ainda</strong><p>Registre sua primeira ordem para acompanhar a operação.</p><?php if (admin_can_write()): ?><a href="cadastrar_ordem.php">Criar uma ordem</a><?php endif; ?></div><?php else: ?><div class="table-scroll"><table class="dashboard-table"><thead><tr><th>Ordem</th><th>Cliente</th><th>Data do serviço</th><th>Status</th><th>Valor</th><th><span class="sr-only">Ação</span></th></tr></thead><tbody>
 <?php foreach ($stats['recent'] as $row): $status = dashboard_status($row['Or_Status']); $class = ['Pendente'=>'warning','Em andamento'=>'info','Concluída'=>'success','Cancelada'=>'danger','Outros'=>'neutral'][$status]; ?><tr><td class="order-id">#<?= dashboard_escape($row['ID_Ordem']) ?></td><td><?= dashboard_escape($row['cli_Nome']) ?></td><td><?= dashboard_escape((new DateTimeImmutable($row['Or_DataServico']))->format('d/m/Y')) ?></td><td><span class="status-badge <?= dashboard_escape($class) ?>"><?= dashboard_escape($status) ?></span></td><td><?= dashboard_escape(dashboard_money($row['Ord_ValorTotal'])) ?></td><td><a href="<?= admin_can_write() ? 'ordens.php?acao=editar&amp;id='.dashboard_escape($row['ID_Ordem']) : 'ordens.php' ?>" aria-label="<?= dashboard_escape('Abrir ordem '.$row['ID_Ordem']) ?>"><?= admin_icon('arrow') ?></a></td></tr><?php endforeach; ?>
 </tbody></table></div><?php endif; ?></article>
 <div class="operations-grid bottom-grid">
 <article class="dashboard-panel"><div class="panel-heading"><div><h2>Ações rápidas</h2><p>Menos cliques para a próxima tarefa</p></div></div><div class="quick-actions">
 <?php foreach ([['cadastrar_cliente.php','Novo cliente','users'],['cadastrar_ordem.php','Nova ordem','clipboard'],['cadastrar_agendamento.php','Agendar serviço','calendar'],['financeiro.php','Contas a receber','wallet']] as [$url,$label,$icon]): if (!admin_route_allowed(admin_role(), $url)) continue; ?><a href="<?= dashboard_escape($url) ?>"><?= admin_icon($icon) ?><span><?= dashboard_escape($label) ?></span><?= admin_icon('arrow') ?></a><?php endforeach; ?>
 </div></article>
 <article class="dashboard-panel financial-summary"><div class="panel-heading"><div><h2>Resumo financeiro do mês</h2><p>Contas com vencimento neste mês</p></div><?= admin_icon('wallet') ?></div><div class="financial-totals"><div><span>Recebido</span><strong><?= dashboard_escape(dashboard_money($stats['received'][5])) ?></strong></div><div><span>Pendente</span><strong><?= dashboard_escape(dashboard_money($stats['pending'][5])) ?></strong></div></div><div class="overdue-total"><span>Pendências vencidas · todos os meses</span><strong><?= dashboard_escape(dashboard_money($stats['overdue'])) ?></strong></div><a class="panel-footer" href="financeiro.php">Consultar financeiro <?= admin_icon('arrow') ?></a></article>
 </div>
 <p class="dashboard-footnote">Horários de Brasília. Indicadores por data do serviço, cadastro ou agendamento. Financeiro por vencimento; contas canceladas não entram no comparativo.</p>
</section>
<script id="dashboard-data" type="application/json"><?= json_encode($chartData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_INVALID_UTF8_SUBSTITUTE) ?></script>
<?php include __DIR__ . '/footer.php'; ?>
