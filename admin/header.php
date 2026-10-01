<?php
require_once __DIR__ . '/bootstrap.php';
require_login();
require_once __DIR__ . '/icons.php';
require_once __DIR__ . '/dashboard_data.php';
$current_page = basename($_SERVER['PHP_SELF']);
$admin_stock = $admin_stock ?? dashboard_stock($con);
$navGroups = [
 ['clientes','Clientes','users','cadastrar_cliente.php','clientes.php','Cadastrar Cliente','Clientes Existentes'],
 ['produtos','Produtos','box','cadastrar_produto.php','produtos.php','Cadastrar Produto','Produtos Existentes'],
 ['servicos','Serviços','leaf','cadastrar_servico.php','servicos.php','Cadastrar Serviço','Serviços Existentes'],
 ['fornecedores','Fornecedores','truck','cadastrar_fornecedor.php','fornecedores.php','Cadastrar Fornecedor','Fornecedores Existentes'],
 ['ordens','Ordens de Serviço','clipboard','cadastrar_ordem.php','ordens.php','Cadastrar Ordem','Ordens Existentes'],
 ['agendamentos','Agendamentos','calendar','cadastrar_agendamento.php','agendamentos.php','Cadastrar Agendamento','Agendamentos Existentes'],
 ['estoque','Estoque','box','cadastrar_estoque.php','estoque.php','Cadastrar Movimentação','Estoque Existente'],
 ['financeiro','Contas a Receber','wallet','cadastrar_conta.php','financeiro.php','Cadastrar Conta','Contas Existentes'],
 ['logins','Logins','key','cadastrar_login.php','logins.php','Cadastrar Login','Logins Existentes']
];
?>
<!DOCTYPE html>
<html lang="pt-BR" class="dark" data-theme="dark">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title><?= $current_page === 'dashboard.php' ? 'Visão Geral' : 'Painel Administrativo' ?> — Golden Jardim</title>
 <script src="../assets/js/admin-theme.js?v=2"></script>
 <link rel="preconnect" href="https://fonts.googleapis.com">
 <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
 <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
 <script src="https://cdn.tailwindcss.com"></script>
 <link rel="stylesheet" href="../assets/css/admin.css?v=3">
 <?php if ($current_page === 'dashboard.php'): ?><link rel="stylesheet" href="../assets/css/dashboard.css?v=2"><?php endif; ?>
</head>
<body class="admin-body font-sans">
<button class="sidebar-overlay" id="sidebarOverlay" aria-label="Fechar menu lateral" hidden></button>
<aside id="sidebar" class="admin-sidebar" aria-label="Menu principal">
 <div class="sidebar-brand">
  <a href="dashboard.php"><span class="brand-symbol"><?= admin_icon('leaf') ?></span><span class="sidebar-text"><strong>Golden Jardim</strong><small>GESTÃO & OPERAÇÕES</small></span></a>
  <button id="closeSidebar" class="icon-button mobile-only" aria-label="Fechar menu"><?= admin_icon('close') ?></button>
 </div>
 <nav class="sidebar-nav">
  <p class="nav-label sidebar-text">PRINCIPAL</p>
  <a href="dashboard.php" class="nav-item <?= $current_page === 'dashboard.php' ? 'active' : '' ?>" <?= $current_page === 'dashboard.php' ? 'aria-current="page"' : '' ?>><?= admin_icon('grid') ?><span class="sidebar-text">Visão Geral</span></a>
  <p class="nav-label sidebar-text">GERENCIAMENTO</p>
  <?php foreach ($navGroups as $group): [$key,$label,$icon,$create,$list,$createLabel,$listLabel] = $group; $active = in_array($current_page, [$create,$list], true); ?>
  <div class="nav-group">
   <button type="button" id="<?= dashboard_escape($key) ?>DropdownBtn" class="nav-item <?= $active ? 'group-active' : '' ?>" aria-expanded="<?= $active ? 'true' : 'false' ?>" aria-controls="<?= dashboard_escape($key) ?>Submenu" aria-label="<?= dashboard_escape($label) ?>">
    <?= admin_icon($icon) ?><span class="sidebar-text"><?= dashboard_escape($label) ?></span>
    <?php if ($key === 'estoque' && count($admin_stock['alerts'])): ?><span class="nav-count" aria-label="<?= count($admin_stock['alerts']) ?> alertas de estoque"><?= count($admin_stock['alerts']) ?></span><?php endif; ?>
    <?= admin_icon('chevron', 'nav-chevron') ?>
   </button>
   <div id="<?= dashboard_escape($key) ?>Submenu" class="nav-submenu <?= $active ? 'is-open' : '' ?>" <?= $active ? '' : 'inert' ?>>
    <div>
    <?php foreach ([[$create,$createLabel],[$list,$listLabel]] as $link): ?>
     <a href="<?= dashboard_escape($link[0]) ?>" class="nav-subitem <?= $current_page === $link[0] ? 'active' : '' ?>" <?= $current_page === $link[0] ? 'aria-current="page"' : '' ?>><?= dashboard_escape($link[1]) ?></a>
    <?php endforeach; ?>
    </div>
   </div>
  </div>
  <?php endforeach; ?>
 </nav>
 <div class="sidebar-bottom"><a href="../index.html" class="nav-item"><?= admin_icon('leaf') ?><span class="sidebar-text">Ver site público</span></a><form action="logout.php" method="POST"><?= csrf_field() ?><button class="nav-item logout-button" type="submit"><?= admin_icon('logout') ?><span class="sidebar-text">Sair do Sistema</span></button></form></div>
</aside>
<div id="mainWrapper" class="admin-wrapper">
 <header class="admin-topbar">
  <div class="topbar-start"><button id="openSidebar" class="icon-button" aria-label="Abrir ou recolher menu" aria-controls="sidebar" aria-expanded="true"><?= admin_icon('menu') ?></button><span class="topbar-breadcrumb">Painel <span>/</span> <strong><?= $current_page === 'dashboard.php' ? 'Visão Geral' : 'Gerenciamento' ?></strong></span></div>
  <div class="topbar-actions">
   <div class="quick-search"><label for="adminSearch" class="sr-only">Buscar uma tela do sistema</label><?= admin_icon('search') ?><input id="adminSearch" type="search" placeholder="Buscar uma tela..." autocomplete="off" aria-controls="searchResults" aria-expanded="false"><div id="searchResults" class="header-popover search-results" hidden></div></div>
   <button id="themeToggle" class="icon-button" aria-label="Alternar tema"><span class="theme-sun"><?= admin_icon('sun') ?></span><span class="theme-moon"><?= admin_icon('moon') ?></span></button>
   <div class="popover-anchor"><button id="notificationsToggle" class="icon-button" aria-label="Notificações de estoque" aria-controls="notificationsPanel" aria-expanded="false"><?= admin_icon('bell') ?><?php if (count($admin_stock['alerts'])): ?><span class="notification-dot"></span><?php endif; ?></button>
    <div id="notificationsPanel" class="header-popover" hidden><strong>Alertas de estoque</strong>
    <?php if (!$admin_stock['ready']): ?><p>Configure o estoque mínimo para receber alertas.</p><?php elseif (!$admin_stock['alerts']): ?><p>Nenhum alerta no momento.</p><?php else: foreach (array_slice($admin_stock['alerts'],0,5) as $alert): ?><a href="estoque.php"><span><?= dashboard_escape($alert['Pro_Nome']) ?></span><small><?= $alert['critical'] ? 'Crítico' : 'Baixo' ?> · <?= dashboard_escape($alert['saldo']) ?> un.</small></a><?php endforeach; endif; ?>
    <a href="estoque.php" class="popover-footer">Ver estoque <?= admin_icon('arrow') ?></a></div>
   </div>
   <div class="popover-anchor"><button id="userMenuToggle" class="user-button" aria-expanded="false" aria-controls="userPanel" aria-label="Menu do usuário"><span class="avatar"><?= dashboard_escape(mb_substr($_SESSION['log_nome'],0,1,'UTF-8')) ?></span><span class="user-info"><strong><?= dashboard_escape($_SESSION['log_nome']) ?></strong><small>Administrador</small></span><?= admin_icon('chevron') ?></button><div id="userPanel" class="header-popover" hidden><strong><?= dashboard_escape($_SESSION['log_nome']) ?></strong><a href="logins.php">Gerenciar acessos</a><form action="logout.php" method="POST"><?= csrf_field() ?><button type="submit"><?= admin_icon('logout') ?> Sair do sistema</button></form></div></div>
  </div>
 </header>
 <main id="adminMain" class="admin-main">
